<?php

namespace App\Console\Commands\Moons;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;

//Jobs
use App\Jobs\ProcessSendEveMailJob;
use App\Jobs\Commands\Moons\MoonRentalInvoiceCreate;

//Library
use Commands\Library\CommandHelper;

//Models
use App\Models\Moon\MoonRental;

class RentalMoonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:RentalMoons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rental Moons jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Declare variables
        $delay = 0;

        //Create the new command helper container
        $task = new CommandHelper('RentalMoonCommand');
        //Add the entry saying the job has started
        $task->SetStartStatus();

        //Get all of the contacts from the rentals group
        $contacts = MoonRental::select('Contact')->groupBy('Contact')->get();

        foreach($contacts as $contact) {
            MoonrentalInvoiceCreate::dispatch($contact, $delay)->onQueue('moons');
        }

        //Set the job as completed
        $task->SetStopStatus();
    }
}
