<?php

namespace Webkid\LaravelBooleanSoftdeletes;

use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SoftDeletesBoolean
{
	/** Indicates if the model is currently force deleting. */
	protected bool $forceDeleting = false;

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
	public function forceDelete(): bool
	{
		$this->forceDeleting = true;

		return tap($this->delete(), function ($deleted) {
			$this->forceDeleting = false;

			if ($deleted) {
				$this->fireModelEvent('forceDeleted', false);
			}
		}) ?? false;
	}

	/**
	 * Restore a soft-deleted model instance.
	 */
	public function restore(): bool
	{
		// If the restoring event does not return false, we will proceed with this
		// restore operation. Otherwise, we bail out so the developer will stop
		// the restore totally. We will clear the deleted timestamp and save.
		if ($this->fireModelEvent('restoring') === false) {
			return false;
		}

		$this->{$this->getIsDeletedColumn()} = false;

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
	 */
	public static function restoring(Closure|string $callback): void
	{
		static::registerModelEvent('restoring', $callback);
	}

	/**
	 * Register a restored model event with the dispatcher.
	 */
	public static function restored(Closure|string|array $callback): void
	{
		static::registerModelEvent('restored', $callback);
	}

	/**
	 * Register a "forceDeleted" model event callback with the dispatcher.
	 */
	public static function forceDeleted(Closure|string|array $callback): void
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
		return defined(static::class . '::IS_DELETED')
			? constant(static::class . '::IS_DELETED')
			: 'is_deleted';
	}

	/**
	 * Get the fully qualified "deleted at" column.
	 */
	public function getQualifiedIsDeletedColumn(): string
	{
		return "{$this->getTable()}.{$this->getIsDeletedColumn()}";
	}

	/**
	 * Perform the actual delete query on this model instance.
	 */
	protected function performDeleteOnModel(): ?bool
	{
		if ($this->forceDeleting) {
			$this->exists = false;

			return (bool) $this->newQueryWithoutScopes()
				->where($this->getKeyName(), $this->getKey())
				->forceDelete();
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

		$this->{$this->getIsDeletedColumn()} = true;
		$columns = [$this->getIsDeletedColumn() => true];

		if ($this->timestamps && ($updatedAt = $this->getUpdatedAtColumn()) !== null) {
			$this->{$updatedAt} = $time;
			$columns[$updatedAt] = $this->fromDateTime($time);
		}

		$query->update($columns);
	}
}
