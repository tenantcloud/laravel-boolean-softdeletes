<?php

namespace Webkid\LaravelBooleanSoftdeletes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SoftDeletingBooleanScope implements Scope
{
	/**
	 * All of the extensions to be added to the builder.
	 *
	 * @var array
	 */
	protected $extensions = ['Restore', 'WithTrashed', 'WithoutTrashed', 'OnlyTrashed'];

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param Model&SoftDeletesBoolean $model
	 */
	public function apply(Builder $builder, Model $model): void
	{
		$builder->where($model->getQualifiedIsDeletedColumn(), 0);
	}

	/**
	 * Extend the query builder with the needed functions.
	 */
	public function extend(Builder $builder): void
	{
		foreach ($this->extensions as $extension) {
			$this->{"add{$extension}"}($builder);
		}

		$builder->onDelete(function (Builder $builder) {
			$column = $this->getIsDeletedColumn($builder);

			return $builder->update([
				$column => 1,
			]);
		});
	}

	/**
	 * Get the "is deleted" column for the builder.
	 */
	protected function getIsDeletedColumn(Builder $builder): string
	{
		if (count((array) $builder->getQuery()->joins) > 0) {
			return $builder->getModel()->getQualifiedIsDeletedColumn();
		}

		return $builder->getModel()->getIsDeletedColumn();
	}

	/**
	 * Add the restore extension to the builder.
	 */
	protected function addRestore(Builder $builder): void
	{
		$builder->macro('restore', static function (Builder $builder) {
			$builder->withTrashed();

			return $builder->update([$builder->getModel()->getIsDeletedColumn() => 0]);
		});
	}

	/**
	 * Add the with-trashed extension to the builder.
	 */
	protected function addWithTrashed(Builder $builder): void
	{
		$builder->macro('withTrashed', function (Builder $builder, $withTrashed = true) {
			if (!$withTrashed) {
				return $builder->withoutTrashed();
			}

			return $builder->withoutGlobalScope($this);
		});
	}

	/**
	 * Add the without-trashed extension to the builder.
	 */
	protected function addWithoutTrashed(Builder $builder): void
	{
		$builder->macro('withoutTrashed', function (Builder $builder) {
			$model = $builder->getModel();

			$builder->withoutGlobalScope($this)->where(
				/** @var Model|SoftDeletesBoolean $model */
				$model->getQualifiedIsDeletedColumn(),
				0
			);

			return $builder;
		});
	}

	/**
	 * Add the only-trashed extension to the builder.
	 */
	protected function addOnlyTrashed(Builder $builder): void
	{
		$builder->macro('onlyTrashed', function (Builder $builder) {
			$model = $builder->getModel();

			$builder->withoutGlobalScope($this)->where(
				/** @var Model|SoftDeletesBoolean $model */
				$model->getQualifiedIsDeletedColumn(),
				1
			);

			return $builder;
		});
	}
}
