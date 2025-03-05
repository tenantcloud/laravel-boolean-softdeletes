<?php

namespace Webkid\LaravelBooleanSoftdeletes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSoftDeletes extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'softdeletes:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate existing soft deletes into is_deleted column';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$table = $this->ask('Provide table name');

		if (!is_string($table) || !Schema::hasTable($table)) {
			$this->error('Wrong table name. Try again, please');

			return;
		}

		$field_name = $this->ask('Provide field name. If is_deleted than leave blank', 'is_deleted');

		$old_field_name = $this->ask('Provide old field name. If deleted_at than leave blank', 'deleted_at');

		if (!is_string($field_name) || !is_string($old_field_name)) {
			$this->error('Wrong field name or old field name.');

			return;
		}

		DB::table($table)->update([
			$field_name => DB::raw("CASE WHEN {$old_field_name} IS NULL THEN 0 ELSE 1 END"),
		]);

		$this->info('Table has been migrated!');
	}
}
