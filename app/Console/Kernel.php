<?php

namespace App\Console;

//Internal Library
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

//Jobs
use App\Jobs\Commands\MiningTaxes\PreFetchMiningTaxesLedgers;
use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObservers;
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPayments;
use App\Jobs\Commands\MiningTaxes\Invoices\UpdateMiningTaxesLateInvoices1st;
use App\Jobs\Commands\MiningTaxes\Invoices\UpdateMiningTaxesLateInvoices15th;
use App\Jobs\Commands\MiningTaxes\MiningTaxesWeeklyInvoicing;
use App\Jobs\Commands\Finances\UpdateAllianceWalletJournalJob;
use App\Jobs\Commands\Finances\UpdateItemPrices as UpdateItemPricesJob;
use App\Jobs\Commands\Data\PurgeUsers as PurgeUsersJob;
use App\Jobs\Commands\Structures\FetchAllianceStructures;
use App\Jobs\Commands\Structures\PurgeAllianceStructures;
use App\Jobs\Commands\Assets\FetchAllianceAssets;
use App\Jobs\Commands\Assets\PurgeAllianceAssets;
use App\Jobs\Commands\MoonRental\UpdateAllianceMoonRentalWorth as UpdateAMRW;

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
        Commands\MiningTaxes\ExecuteMiningTaxesObserversCommand::class,
        Commands\MiningTaxes\ExecuteMiningTaxesLedgersCommand::class,
        Commands\MiningTaxes\ExecuteSendMiningTaxesInvoiceCommand::class,
        Commands\MiningTaxes\ExecuteProcesssMiningTaxesPaymentsCommand::class,
        Commands\Structures\ExecuteFetchAllianceStructuresCommand::class,
        Commands\Structures\ExecuteFetchAllianceAssetsCommand::class,
        Commands\Files\ImportAllianceMoons::class,
        Commands\MoonRental\ExecuteUpdateAllianceMoonRentalWorth::class,
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

        /**
         * Purge Data Schedule
         */
        $schedule->job(new PurgeUsersJob)
                 ->weekly();

        /**
         * Finances Update Schedule
         */
        $schedule->job(new UpdateAllianceWalletJournalJob)
                 ->hourlyAt('45')
                 ->withoutOverlapping();

        /**
         * Item Update Schedule
         */
        $schedule->job(new UpdateItemPricesJob)
                 ->hourlyAT('30')
                 ->withoutOverlapping();

        /**
         * Mining Tax Schedule
         */
        $schedule->job(new FetchMiningTaxesObservers)
                 ->dailyAt('20:00')
                 ->withoutOverlapping();
        $schedule->job(new PreFetchMiningTaxesLedgers)
                 ->dailyAt('22:00')
                 ->withoutOverlapping();
        $schedule->job(new MiningTaxesWeeklyInvoicing)
                 ->weeklyOn(1, '06:00')
                 ->withoutOverlapping();
        $schedule->job(new ProcessMiningTaxesPayments)
                 ->hourlyAt('15')
                 ->withoutOverlapping();
        $schedule->job(new UpdateMiningTaxesLateInvoices1st)
                 ->monthlyOn(1, '16:00')
                 ->withoutOverlapping();
        $schedule->job(new UpdateMiningTaxesLateInvoices15th)
                 ->monthlyOn(15, '16:00')
                 ->withoutOverlapping();
        $schedule->job(new UpdateAMRW)
                 ->dailyAt('13:00')
                 ->withoutOverlapping();
        
        /**
         * Alliance Structure and Assets Schedule
         */
        $schedule->job(new FetchAllianceStructures)
                 ->dailyAt('21:00')
                 ->withoutOverlapping();
        $schedule->job(new FetchAllianceAssets)
                 ->hourlyAt('15')
                 ->withoutOverlapping();
        $schedule->job(new PurgeAllianceStructures)
                 ->monthlyOn(2, '14:00')
                 ->withoutOverlapping();
        $schedule->job(new PurgeAllianceAssets)
                 ->monthlyOn(2, '15:00')
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

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'UTC';
    }
}
