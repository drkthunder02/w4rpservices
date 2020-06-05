<?php

namespace App\Console\Commands\RentalMoons;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Library
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\RentalMoons\SendMoonRentalPaymentReminderJob;
use App\Jobs\Commands\RentalMoons\UpdateMoonRentalPaidState;

class AllianceRentalMoonInvoiceCreationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:RentalInvoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out rental invoice reminders';

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
        //Set the task as started
        $task = new CommandHelper('AllianceRentalInvoice');
        $task->SetStartStatus();

        //Send the job for the invoice creation command
        SendMoonRentalPaymentReminderJob::dispatch()->delay(Carbon::now()->addSeconds(10));

        //Update the paid state for the moons
        UpdateMoonRentalPaidState::dispatch()->delay(Carbon::now()->addSeconds(600));

        //Set the task as stopped
        $task->SetStopStatus();
    }
}