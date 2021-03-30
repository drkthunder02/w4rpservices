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
        Commands\Data\PurgeUsers::class,
        Commands\Data\Test::class,
        Commands\Eve\ItemPricesUpdateCommand::class,
        Commands\Finances\UpdateAllianceWalletJournal::class,
        Commands\MiningTaxes\MiningTaxesInvoices::class,
        Commands\MiningTaxes\MiningTaxesInvoicesNew::class,
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

        //Test rung to help figure out what is going on.
        $schedule->command('inspire')->everyMinute();

        /**
         * Purge Data Schedule
         */
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
                 ->hourlyAt('30');

        /**
         * Mining Tax Schedule
         */
        $schedule->command('MiningTax:Observers')
                 ->dailyAt('22:00');
        $schedule->command('MiningTax:Ledgers')
                 ->dailyAt('20:00');
        $schedule->command('MiningTax:Invoices')
                 ->weeklyOn(1, '6:00');
        $schedule->command('MiningTax:Payments')
                 ->hourlyAt('15');
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
