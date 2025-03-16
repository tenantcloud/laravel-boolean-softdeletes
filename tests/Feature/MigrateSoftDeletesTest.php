<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Webkid\LaravelBooleanSoftdeletes\Commands\MigrateSoftDeletes;

#[CoversClass(MigrateSoftDeletes::class)]
class MigrateSoftDeletesTest extends TestCase
{
	use DatabaseMigrations;
	use WithWorkbench;

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
	}

	/**
	 * Test the soft deletes migration command.
	 */
	public function testCommandMigratesSoftDeletes(): void
	{
		// Insert test data
		DB::table('test_items')->insert([
			['id' => 1, 'deleted_at' => null, 'is_deleted' => false],
			['id' => 2, 'deleted_at' => now(), 'is_deleted' => false],
		]);

		// Run the command with mock user inputs
		$this->artisan(MigrateSoftDeletes::class)
			->expectsQuestion('Provide table name', 'test_items')
			->expectsQuestion('Provide field name. If is_deleted than leave blank', 'is_deleted')
			->expectsQuestion('Provide old field name. If deleted_at than leave blank', 'deleted_at')
			->expectsOutput('Table has been migrated!')
			->assertExitCode(0);

		// Assert database updates
		$this->assertDatabaseHas('test_items', ['id' => 1, 'is_deleted' => false]);
		$this->assertDatabaseHas('test_items', ['id' => 2, 'is_deleted' => true]);
	}
}
