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
        Commands\Moons\UpdateMoonPriceCommand::class,
        Commands\Finances\HoldingFinancesCommand::class,
        Commands\Moons\MoonMailerCommand::class,
        Commands\Structures\GetStructuresCommand::class,
        Commands\Assets\GetAssetsCommand::class,
        Commands\Users\PurgeUsers::class,
        Commands\Flex\FlexStructureCommand::class,
        Commands\Data\EmptyJumpBridges::class,
        Commands\Wormholes\PurgeWormholes::class,
        Commands\Finances\SovBillsCommand::class,
        Commands\Data\CleanStaleDataCommand::class,
        Commands\Moons\MoonsUpdateCommand::class,
        Commands\Moons\RentalMoonCommand::class,
        //Commands\Market\GetMarketDataCommand::class,
        //Commands\Market\PurgeMarketDataCommand::class,
        //Commands\PublicContracts\PublicContractsCommand::class,
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
        //Command to get the holding journal finances
        $schedule->command('services:HoldingJournal')
                ->hourly()
                ->withoutOverlapping();
        //Command to update moon rental pricing
        $schedule->command('services:UpdateMoonPrice')
                ->hourlyAt('30')
                ->withoutOverlapping();
        //Get the corps within the alliance
        $schedule->command('services:GetCorps')
                 ->monthlyOn(1, '09:00')
                 ->withoutOverlapping();
        //Update the moons, and send out mails for current moon rentals
        $schedule->command('services:MoonMailer')
                 ->monthlyOn(1, '00:01');
        //Get the structures within the alliance and their information
        $schedule->command('services:GetStructures')
                ->dailyAt('09:00')
                ->withoutOverlapping();
        //Get the assets for the alliance.  Only saves the Liquid Ozone for jump bridges
        $schedule->command('services:GetAssets')
                ->hourlyAt('22')
                ->withoutOverlapping();
        //Clean old data from the database
        $schedule->command('services:CleanData')
                ->monthlyOn(15, '18:00');
        //Purge users from the database which don't belong and reset the user roles as necessary
        $schedule->command('services:PurgeUsers')
                ->dailyAt('23:00')
                ->withoutOverlapping();
        //Mail about payments for flex structures
        $schedule->command('services:FlexStructures')
                ->monthlyOn(2, '00:01')
                ->withoutOverlapping();
        //Wormhole data purge
        $schedule->command('services:PurgeWormholeData')
                ->hourlyAt(20);
        //Purge old data from the database
        $schedule->command('services:CleanData')
                 ->weekly(7, '11:00')
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
