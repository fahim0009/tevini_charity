<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CompanyDetail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DeductStandingDonation::class,
        Commands\DonorMailCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('donation:deduct-standing')->daily();
        $schedule->command('donation:deduct-standing')
            ->daily()
            ->withoutOverlapping()
            ->onOneServer();
        
        try {
            $companyDetail = CompanyDetail::first();
            $autoPaymentTime = $companyDetail->auto_payment_time ?? '16:30';
            
            // FIX: Extract only HH:MM to prevent Laravel dailyAt() bug with seconds
            $autoPaymentTime = substr($autoPaymentTime, 0, 5);
            
        } catch (\Exception $e) {
            $autoPaymentTime = '16:30';
        }

        // $schedule->command('payments:process-charity')->dailyAt($autoPaymentTime);
        $schedule->command('payments:process-charity')
                    ->dailyAt($autoPaymentTime)
                    ->withoutOverlapping()
                    ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}