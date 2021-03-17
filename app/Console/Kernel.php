<?php

namespace App\Console;

//Internal Library
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
         * Finance Commands
         */
        Commands\Finances\UpdateAllianceWalletJournal::class,
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
                 ->weekly(7, '11:00')
                 ->withoutOverlapping();
        $schedule->command('data:PurgeCorpLedgers')
                 ->monthly();
        $schedule->command('data:PurgeUsers')
                 ->dailyAt('23:00');

        /**
         * Finances Update Schedule
         */
        $schedule->command('finances:UpdateJournals')
                 ->hourlyAt('45')
                 ->withoutOverlapping();

        /**
         * Item Update Schedule
         */
        $schedule->command('services:ItemPriceUpdate')
                 ->hourlyAt('30')
                 ->withoutOverlapping();

        /**
         * Mining Tax Schedule
         */
        //$schedule->command('MiningTax:Observers')
        //         ->dailyAt('22:00')
        //         ->withoutOverlapping();
        $schedule->command('MiningTax:Ledgers')
                 ->dailyAt('20:00')
                 ->withoutOverlapping();
        $schedule->command('MiningTax:Invoices')
                 ->weeklyOn(1, '8:00')
                 ->withoutOverlapping();
        $schedule->command('MiningTax:Payments')
                 ->hourlyAt('15')
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
