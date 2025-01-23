<?php

namespace Webkid\LaravelBooleanSoftdeletes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrateSoftDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'softdeletes:migrate {table} {new_field_name} {old_field_name} {delete_old_field_name}';

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
        $table = $this->argument('table');
        if (!$table) {
            $table = $this->ask('Provide table name');
        }

        if (!Schema::hasTable($table)) {
            $this->error('Wrong table name. Try again, please');
        }


        $field_name = $this->argument('new_field_name');
        if (!$field_name) {
            $field_name = $this->ask('Provide field name. If is_deleted than leave blank', 'is_deleted');
        }

        if (!Schema::hasColumn(
            $table, $field_name
        )
        ) {
            $this->error('Wrong field name. ' .
                ' Table: ' . $table . ' has no column: ' . $field_name .
                '. Try again, please');
        }


        $old_field_name = $this->argument('old_field_name');
        if (!$old_field_name) {
            $old_field_name = $this->ask('Provide old field name. If deleted_at than leave blank', 'deleted_at');
        }

        if (!Schema::hasColumn(
            $table, $old_field_name
        )
        ) {
            $this->error('Wrong old field name. ' .
                ' Table: ' . $table . ' has no column: ' . $old_field_name .
                '. Try again, please');
        }

        DB::table($table)->whereNull($old_field_name)->update([$field_name => 0]);
        DB::table($table)->whereNotNull($old_field_name)->update([$field_name => 1]);
        if ($this->argument('delete_old_field_name')) {
            Schema::table($table, function (Blueprint $table) use ($old_field_name) {
                $table->dropColumn($old_field_name);
            });
        }
    }
}
