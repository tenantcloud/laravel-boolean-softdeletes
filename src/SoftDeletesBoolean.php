<?php

namespace Webkid\LaravelBooleanSoftdeletes;

use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SoftDeletesBoolean
{
	/**
	 * Indicates if the model is currently force deleting.
	 *
	 * @var bool
	 */
	protected $forceDeleting = false;

	/**
	 * Boot the soft deleting trait for a model.
	 */
	public static function bootSoftDeletesBoolean(): void
	{
		static::addGlobalScope(new SoftDeletingBooleanScope());
	}

	/**
	 * Force a hard delete on a soft deleted model.
	 */
	public function forceDelete(): ?bool
	{
		$this->forceDeleting = true;

		return tap($this->delete(), function ($deleted) {
			$this->forceDeleting = false;

			if ($deleted) {
				$this->fireModelEvent('forceDeleted', false);
			}
		});
	}

	/**
	 * Restore a soft-deleted model instance.
	 */
	public function restore(): ?bool
	{
		// If the restoring event does not return false, we will proceed with this
		// restore operation. Otherwise, we bail out so the developer will stop
		// the restore totally. We will clear the deleted timestamp and save.
		if ($this->fireModelEvent('restoring') === false) {
			return false;
		}

		$this->{$this->getIsDeletedColumn()} = 0;

		// Once we have saved the model, we will fire the "restored" event so this
		// developer will do anything they need to after a restore operation is
		// totally finished. Then we will return the result of the save call.
		$this->exists = true;

		$result = $this->save();

		$this->fireModelEvent('restored', false);

		return $result;
	}

	/**
	 * Determine if the model instance has been soft-deleted.
	 */
	public function trashed(): bool
	{
		return (bool) $this->{$this->getIsDeletedColumn()};
	}

	/**
	 * Register a restoring model event with the dispatcher.
	 *
	 * @param Closure|string $callback
	 */
	public static function restoring($callback)
	{
		static::registerModelEvent('restoring', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 *
	 * @param Closure|string|array $callback
	 */
	public static function restored($callback): void
	{
		static::registerModelEvent('restored', $callback);
	}

	/**
	 * Register a "forceDeleted" model event callback with the dispatcher.
	 *
	 * @param Closure|string|array $callback
	 */
	public static function forceDeleted($callback): void
	{
		static::registerModelEvent('forceDeleted', $callback);
	}

	/**
	 * Determine if the model is currently force deleting.
	 */
	public function isForceDeleting(): bool
	{
		return $this->forceDeleting;
	}

	/**
	 * Get the name of the "deleted at" column.
	 */
	public function getIsDeletedColumn(): string
	{
		return defined('static::IS_DELETED') ? constant('static::IS_DELETED') : 'is_deleted';
	}

	/**
	 * Get the fully qualified "deleted at" column.
	 */
	public function getQualifiedIsDeletedColumn(): string
	{
		return $this->getTable() . '.' . $this->getIsDeletedColumn();
	}

	/**
	 * Perform the actual delete query on this model instance.
	 */
	protected function performDeleteOnModel(): ?bool
	{
		if ($this->forceDeleting) {
			$this->exists = false;

			return $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey())->forceDelete();
		}

		$this->runSoftDelete();

		return null;
	}

	/**
	 * Perform the actual delete query on this model instance.
	 */
	protected function runSoftDelete(): void
	{
		$query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());

		$time = $this->freshTimestamp();

		$columns = [$this->getIsDeletedColumn() => 1];

		$this->{$this->getIsDeletedColumn()} = 1;

		if ($this->timestamps && null !== $this->getUpdatedAtColumn()) {
			$this->{$this->getUpdatedAtColumn()} = $time;

			$columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
		}

		$query->update($columns);
	}
}
