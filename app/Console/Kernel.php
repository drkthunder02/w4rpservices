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
        Commands\Data\Test::class,
        /**
         * Eve Commands
         */
        Commands\Eve\ItemPricesUpdateCommand::class,
        /**
         * Mining Tax Commands
         */
        Commands\MiningTaxes\MiningTaxesInvoices::class,
        Commands\MiningTaxes\MiningTaxesLedgers::class,
        Commands\MiningTaxes\MiningTaxesObservers::class,
        Commands\MiningTaxes\MiningTaxesPayments::class,
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

        /**
         * Mining Tax Schedule
         */
        $schedule->command('MiningTaxes:Observers')
                 ->dailyAt('22:00')
                 ->withoutOverlapping();
        $schedule->command('MiningTaxes:Ledgers')
                 ->dailyAt('20:00')
                 ->withoutOverlapping();
        /*
        $schedule->command('MiningTaxes:Invoices')
                 ->weeklyOn(1, '8:00')
                 ->withoutOverlapping();
        $schedule->command('MiningTaxes:Payments')
                 ->hourlyAt('15')
                 ->withoutOverlapping();
        */
        
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
