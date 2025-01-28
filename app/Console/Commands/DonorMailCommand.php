<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DonorMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'queue:work';
    protected $signature = 'donor:mail {--once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if ($this->option('once')) {
            Log::info('Every 5 minute email send.');
            // Perform the task once
            $this->info('Processing one batch of donor mails.');
            return;
        }
    
        // Default behavior
        $this->info('Processing all donor mails.');
    }
}
