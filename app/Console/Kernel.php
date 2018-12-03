<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CorpJournal::class,
        Commands\getCorps::class,
        Commands\sendMail::class,
        Commands\UpdateMoonPricing::class,
        Commands\DumpFleets::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('services:corpjournal')
                 ->hourly()
                 ->withoutOverlapping();
        $schedule->command('services:getcorps')
                 ->daily()
                 ->withoutOverlapping();
        $schedule->command('services:updatemoonprice')
                 ->hourly()
                 ->withoutOverlapping();
        $schedule->command('services:dumpfleets')
                 ->dailyAt('05:00')
                 ->withoutOverlapping();
        //$schedule->command('services:sendmail')
        //         ->monthlyOn(1, '09:00')
        //         ->withoutOverlapping();
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
