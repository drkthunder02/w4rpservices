<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

use Commands\Library\CommandHelper;
use App\Library\Esi\Mail;

use App\User;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\ScheduledTask\ScheduleJob;
use App\Models\Market\MonthlyMarketTax;
use App\Models\Mail\EveMail;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:sendmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to a character for taxes owed.';

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
     * Gather the taxes needed and add them together.
     * Send a mail to the character owning the ESI scope with the taxes
     * owed to the holding corp
     *
     * @return mixed
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Retrieve the token for main character to send mails from
        $token = EsiToken::where(['character_id' => 93738489])->first();

        //Set the date
        $date = Carbon::now()->subMonth();
        //Set the mail helper variable
        $mHelper = new Mail();
        //Create a new esi container and authentication
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token->refresh_token,
        ]);
        $esi = new Eseye($authentication);

        //Get the full list of bills to send out
        $bills = MonthlyMarketTax::where(['month' => $date->month, 'year' => $date->year])->get();
        //For each of the bills send a mail out
        foreach($bills as $bill) {
            //Send a mail out with the bill
            $subject = 'Market Taxes Owed';
            $body = 'Month: ' . 
                    $bill->month .
                    '<br>Market Taxes Owed: ' .
                    $bill->tax_owed .
                    '<br>Please remit to Spatial Forces';
            try {
                $esi->setBody([
                    'mail' => [
                        'approved_cost'=> 50000,
                        'body' => $body,
                        'recipients' => [
                            'recipient_id' => $bill->character_id,
                            'recipient_type' => 'character',
                        ],
                        'subject' => $subject,
                    ],
                ])->invoke('post', '/characters/{character_id}/mail/', [
                    'character_id'=> 93738489,
                ]);
                /*
                $esi->setBody([
                    'approved_cost' => 50000,
                    'body' => $body,
                    'recipients' => [
                        'recipient_id' => $bill->character_id,
                        'recipient_type' => 'character',
                    ],
                    'subject' => $subject,
                ])->invoke('post', '/characters/{character_id}/mail/', [
                    'character_id'=> 93738489,
                ]);
                */
            } catch(RequestFailedException $e) {
                //
            }
            
            $error = $mHelper->SendMail($bill->character_id, $bill->tax_owed, $subject, $body);
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
