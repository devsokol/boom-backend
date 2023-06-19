<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixPostgreSqlPrimaryKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:postgresql-primary-keys-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetting primary keys after restoring the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Do you wish to continue reset primary keys? [yes|no]')) {
            if (config('database.default') === 'pgsql') {
                $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

                foreach ($tables as $table) {
                    $tableHasColumn = Schema::hasColumn($table, 'id');

                    if ($tableHasColumn) {
                        $records = DB::select("SELECT MAX(id) FROM {$table}");

                        if ($records[0]?->max) {
                            DB::select("SELECT setval('\"{$table}_id_seq\"', (SELECT MAX(id) FROM {$table}))");
                        }
                    }
                }

                $this->info('The command was successful!');
            } else {
                $this->warn('This command can only be applied to the PostgreSQL database!');

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
