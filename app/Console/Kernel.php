<?php

namespace App\Console;

//Internal Library
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

//Jobs
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesLedgersJob;
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObserversJob;
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPaymentsJob;
use App\Jobs\Commands\MiningTaxes\SendMiningTaxesInvoicesJob;
use App\Jobs\Commands\Finances\UpdateAllianceWalletJournalJob;
use App\Jobs\Commands\Finances\UpdateItemPricesJob;
use App\Jobs\Commands\Data\PurgeUsersJob;

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
        Commands\MiningTaxes\MiningTaxesDataCleanup::class,
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
        //Schedule Monitor Jobs
        $schedule->command('schedule-monitor:sync')->dailyAt('04:56');
        $schedule->command('schedule-monitor:clean')->daily();

        //Horizon Graph Schedule
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        //Test rung to help figure out what is going on.
        $schedule->command('inspire')->everyMinute();

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
        $schedule->job(new FetchMiningTaxesObserversJob)
                 ->timezone('UTC')
                 ->dailyAt('22:00');
        $schedule->job(new PreFetchMiningTaxesLedgersJob)
                 ->timezone('UTC')
                 ->dailyAt('20:00');
        $schedule->job(new SendMiningTaxesInvoicesJob)
                 ->timezone('UTC')
                 ->weeklyOn(1, '06:00');
        $schedule->job(new ProcessMiningTaxesPaymentsJob)
                 ->timezone('UTC')
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
