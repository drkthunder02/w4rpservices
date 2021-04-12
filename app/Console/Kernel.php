<?php

namespace App\Console;

//Internal Library
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

//Jobs
use App\Jobs\Commands\MiningTaxes\PreFetchMiningTaxesLedgers;
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObservers;
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPayments;
use App\Jobs\Commands\MiningTaxes\SendMiningTaxesInvoices;
use App\Jobs\Commands\MiningTaxes\UpdateMiningTaxesLateInvoices1st;
use App\Jobs\Commands\MiningTaxes\UpdateMiningTaxesLateInvoices15th;
use App\Jobs\Commands\Finances\UpdateAllianceWalletJournal as UpdateAllianceWalletJournalJob;
use App\Jobs\Commands\Finances\UpdateItemPrices as UpdateItemPricesJob;
use App\Jobs\Commands\Data\PurgeUsers as PurgeUsersJob;

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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Schedule Monitor Jobs
        $schedule->command('schedule-monitor:sync')->dailyAt('04:56');
        $schedule->command('schedule-monitor:clean')->daily();

        //Horizon Graph Schedule
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        //Test rung to help figure out what is going on.
        //$schedule->command('inspire')->everyMinute();

        /**
         * Purge Data Schedule
         */
        $schedule->job(new PurgeUsersJob)
                 ->weekly();

        /**
         * Finances Update Schedule
         */
        $schedule->job(new UpdateAllianceWalletJournalJob)
                 ->timezone('UTC')
                 ->hourlyAt('45')
                 ->withoutOverlapping();

        /**
         * Item Update Schedule
         */
        $schedule->job(new UpdateItemPricesJob)
                 ->timezone('UTC')
                 ->hourlyAT('30')
                 ->withoutOverlapping();

        /**
         * Mining Tax Schedule
         */
        $schedule->job(new FetchMiningTaxesObservers)
                 ->timezone('UTC')
                 ->dailyAt('22:00');
        $schedule->job(new PreFetchMiningTaxesLedgers)
                 ->timezone('UTC')
                 ->dailyAt('20:00');
        $schedule->job(new SendMiningTaxesInvoices)
                 ->timezone('UTC')
                 ->weeklyOn(1, '06:00');
        $schedule->job(new ProcessMiningTaxesPayments)
                 ->timezone('UTC')
                 ->hourlyAt('15');
        $schedule->job(new UpdateMiningTaxesLateInvoices1st)
                 ->timezone('UTC')
                 ->monthlyOn(1, '16:00');
        $schedule->job(new UpdateMiningTaxesLateInvoices15th)
                 ->timezone('UTC')
                 ->monthlyOn(15, '16:00');
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
