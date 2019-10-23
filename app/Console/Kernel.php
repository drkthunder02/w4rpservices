<?php

namespace App\Console;

//Internal Library
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

//Library
use Commands\Library\CommandHelper;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GetCorpsCommand::class,
        Commands\UpdateMoonPriceCommand::class,
        Commands\HoldingFinancesCommand::class,
        Commands\MoonMailerCommand::class,
        Commands\PiTransactionsCommand::class,
        Commands\GetStructuresCommand::class,
        Commands\GetAssetsCommand::class,
        Commands\GetEveContractsCommand::class,
        Commands\PurgeUsers::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('services:HoldingJournal')
                ->hourly()
                ->withoutOverlapping();
        $schedule->command('services:UpdateMoonPrice')
                ->hourly()
                ->withoutOverlapping();
        $schedule->command('services:GetCorps')
                 ->monthlyOn(1, '09:00')
                 ->withoutOverlapping();
        $schedule->command('services:MoonMailer')
                ->monthlyOn(1, '00:01')
                ->withoutOverlapping();
        $schedule->command('services:PiTransactions')
                ->hourly()
                ->withoutOverlapping();
        $schedule->command('services:GetStructures')
                ->dailyAt('09:00')
                ->withoutOverlapping();
        $schedule->command('services:GetAssets')
                ->hourlyAt('22')
                ->withoutOverlapping();
        $schedule->command('services:CleanData')
                ->monthlyOn(1, '18:00');
        $schedule->command('services:PurgeUsers')
                ->dailyAt('23:00')
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
