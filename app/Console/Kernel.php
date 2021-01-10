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
        Commands\Data\PurgeCorpMoonLedgers::class,
        /**
         * Assets Commands
         */
        Commands\Assets\GetAssetsCommand::class,
        /**
         * Corp Commands
         */
        Commands\Corps\GetCorpsCommand::class,
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
         * Flex Commands
         */
        Commands\Flex\FlexStructureCommand::class,
        /**
         * Moon Commands
         */
        Commands\Moons\MoonsUpdateCommand::class,
        /**
         * Rental Moon Commands
         */
        Commands\RentalMoons\AllianceRentalMoonInvoiceCreationCommand::class,
        Commands\RentalMoons\AllianceRentalMoonUpdatePricingCommand::class,
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
