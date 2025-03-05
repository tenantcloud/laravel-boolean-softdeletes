<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean;

/**
 * Test case for the SoftDeletesBoolean trait.
 */
class SoftDeletesBooleanTest extends TestCase
{
	use DatabaseMigrations;
	use WithWorkbench;

	protected Model $model;

	protected function setUp(): void
	{
		parent::setUp();

		// Run migration for test model
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		$this->model = new class () extends Model {
			use SoftDeletesBoolean;

			public $timestamps = false;

			protected $table = 'test_items';
		};
	}

	public function testSoftDeletesModel(): void
	{
		$model = $this->model::create();

		// Ensure model is not deleted initially
		$this->assertFalse($model->trashed());

		// Soft delete the model
		$model->delete();

		// Ensure it is marked as deleted
		$this->assertTrue($model->trashed());
		$this->assertDatabaseHas('test_items', ['id' => $model->id, 'is_deleted' => true]);
	}

	public function testRestoresSoftDeletedModel(): void
	{
		$model = $this->model::create();
		$model->delete();

		// Restore the model
		$model->restore();

		// Ensure it is no longer deleted
		$this->assertFalse($model->trashed());
		$this->assertDatabaseHas('test_items', ['id' => $model->id, 'is_deleted' => false]);
	}

	public function testForceDeletesModel(): void
	{
		$model = $this->model::create();

		// Ensure the model exists
		$this->assertDatabaseHas('test_items', ['id' => $model->id]);

		// Force delete
		$model->forceDelete();

		// Ensure the model is completely removed from DB
		$this->assertDatabaseMissing('test_items', ['id' => $model->id]);
	}

	public function testDoesNotForceDeleteIfSoftDeleted(): void
	{
		$model = $this->model::create();

		// Soft delete
		$model->delete();

		// Ensure still exists but marked as deleted
		$this->assertDatabaseHas('test_items', ['id' => $model->id, 'is_deleted' => true]);
	}
}
