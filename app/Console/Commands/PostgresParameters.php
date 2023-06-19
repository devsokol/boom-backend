<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PostgresParameters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:show-postgres-params';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show postgres params';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $records = DB::select("SELECT name, setting FROM pg_settings");

        dd($records);

        return Command::SUCCESS;
    }
}
