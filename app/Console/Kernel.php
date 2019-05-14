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
        Commands\CorpJournalCommand::class,
        Commands\GetCorpsCommand::class,
        Commands\UpdateMoonPriceCommand::class,
        Commands\CalculateMarketTaxCommand::class,
        Commands\HoldingFinancesCommand::class,
        Commands\MoonMailerCommand::class,
        Commands\PiTransactionsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('services:CorpJournal')
                 ->hourly()
                 ->withoutOverlapping();
        $schedule->command('services:HoldingJournal')
                ->hourly()
                ->withoutOverlapping();
        $schedule->command('services:UpdateMoonPrice')
                ->hourly()
                ->withoutOverlapping();
        $schedule->command('services:GetCorps')
                 ->monthlyOn(1, '09:00')
                 ->withoutOverlapping();
        $schedule->command('services:CalculateMarketTax')
                 ->monthlyOn(1, '08:00')
                 ->withoutOverlapping();
        $schedule->command('services:MoonMailer')
                ->monthlyOn(1, '00:01')
                ->withoutOverlapping();
        $schedule->command('services:PiTransactions')
                ->hourly()
                ->withoutOverlapping();

        //Horizon Graph Schedule
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
