<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Demo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boom:install-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For upload demonstration data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('app.env') !== 'production') {
            if ($this->confirm('Do you wish to continue upload demonstration data? [yes|no]')) {
                $this->call('db:seed', ['--class' => 'ProjectSeeder']);
            }
        } else {
            $this->warn('You cannot run this operation in production mode!');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
