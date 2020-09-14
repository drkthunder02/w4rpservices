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
        Commands\Corps\GetCorpsCommand::class,
        Commands\Finances\HoldingFinancesCommand::class,
        Commands\Structures\GetStructuresCommand::class,
        Commands\Assets\GetAssetsCommand::class,
        Commands\Users\PurgeUsers::class,
        Commands\Flex\FlexStructureCommand::class,
        Commands\Data\EmptyJumpBridges::class,
        Commands\Finances\SovBillsCommand::class,
        Commands\Data\CleanStaleDataCommand::class,
        Commands\Moons\MoonsUpdateCommand::class,
        Commands\Data\PurgeCorpMoonLedgers::class,
        Commands\Eve\ItemPricesUpdateCommand::class,
        /**
         * Rental Moon Commands
         */
        Commands\RentalMoons\AllianceRentalMoonInvoiceCreationCommand::class,
        Commands\RentalMoons\AllianceRentalMoonUpdatePricingCommand::class,
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
         * Rentals / Flex Schedule
         */
        $schedule->command('services:UpdateRentalPrice')
                 ->dailyAt('11:00')
                 ->withoutOverlapping();
        $schedule->command('services:FlexStructures')
                 ->monthlyOn(2, '00:01');

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
        $schedule->command('services:CleanData')
                 ->weekly(7, '11:00');
        $schedule->command('data:PurgeCorpLedgers')
                 ->monthly();
        $schedule->command('services:PurgeUsers')
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
