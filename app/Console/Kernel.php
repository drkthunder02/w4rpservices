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
        /**
         * Data Commands
         */
        Commands\Data\PurgeUsers::class,
        Commands\Data\EmptyJumpBridges::class,
        Commands\Data\CleanStaleDataCommand::class,
        Commands\Data\GetCorpsCommand::class,
        Commands\Data\Test::class,
        /**
         * Assets Commands
         */
        Commands\Assets\GetAssetsCommand::class,
        /**
         * Eve Commands
         */
        Commands\Eve\ItemPricesUpdateCommand::class,
        /**
         * Finances Commands
         */
        Commands\Finances\HoldingFinancesCommand::class,
        Commands\Finances\SovBillsCommand::class,
        /**
         * Structures Command
         */
        Commands\Structures\GetStructuresCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Horizon Graph Schedule
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        /**
         * Moon Update Schedule
         */

        /**
         * System Rental Schedule
         */
        $schedule->command('services:SystemRentals')
                 ->monthlyOn(1, '00:01')
                 ->withoutOverlapping();
        /**
         * Holding Corp Finance Schedule
         */
        $schedule->command('services:HoldingJournal')
                 ->hourlyAt('45')
                 ->withoutOverlapping();

        /**
         * Get Information Schedule
         */
        $schedule->command('services:GetCorps')
                 ->monthlyOn(1, '09:00');
        $schedule->command('services:GetStructures')
                 ->dailyAt('09:00');
        $schedule->command('services:GetAssets')
                 ->hourlyAt('22');     

        /**
         * Purge Data Schedule
         */
        $schedule->command('data:CleanData')
                 ->weekly(7, '11:00');
        $schedule->command('data:PurgeCorpLedgers')
                 ->monthly();
        $schedule->command('data:PurgeUsers')
                 ->dailyAt('23:00');

        /**
         * Item Update Schedule
         */
        $schedule->command('services:ItemPriceUpdate')
                 ->hourlyAt('30')
                 ->withoutOverlapping();
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
