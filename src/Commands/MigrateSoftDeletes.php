<?php

namespace Webkid\LaravelBooleanSoftdeletes\Commands;

use Exception;
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

		if (!Schema::hasTable($table)) {
			$this->error('Wrong table name. Try again, please');
		}

		$field_name = $this->ask('Provide field name. If is_deleted than leave blank', 'is_deleted');

		$old_field_name = $this->ask('Provide old field name. If deleted_at than leave blank', 'deleted_at');

		try {
			DB::table($table)->orderBy('id', 'desc')->chunk(100, function ($items) use ($table, $field_name, $old_field_name) {
				foreach ($items as $item) {
					DB::table($table)
						->where('id', $item->id)
						->update([$field_name => $item->{$old_field_name} !== null]);
				}
			});

			$this->info('Table has been migrated!');
		} catch (Exception $e) {
			$this->error($e->getMessage());
		}
	}
}
