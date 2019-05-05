<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

//Jobs
use App\Jobs\SendEveMailJob;

//Libraries
use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;
use App\Library\Structures\StructureTaxHelper;
use App\Library\Esi\Esi;

//Models
use App\Models\Market\MonthlyMarketTax;
use App\Models\ScheduledTask\ScheduleJob;
use App\Models\Corporation\CorpStructure;
use App\Models\User\UserToCorporation;
use App\Models\Jobs\JobSendEveMail;

class CalculateMarketTaxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:CalculateMarketTax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the market taxes owed to the holding corporation and store in the database.';

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
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Setup helper classes
        $hFinances = new FinanceHelper();
        $sHelper = new StructureTaxHelper();
        $start = Carbon::now()->startOfMonth()->subMonth();
        $end = Carbon::now()->endOfMOnth()->subMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        //Get the set of corporations from the structure table
        $corps = CorpStructure::select('corporation_id')->groupBy('corporation_id')->get();
        $this->line('Got all of the  corps with markets.' . sizeof($corps));
        foreach($corps as $corp) {
            if($corp->corporation_id != 98287666) {
                $finalTaxes = $sHelper->GetTaxes($corp->corporation_id, 'Market', $start, $end);
                if($finalTaxes < 0.00) {
                    $finalTaxes = 0.00;
                }

                //Get the info about the structures from the database
                $info = CorpStructure::where(['corporation_id' => $corp->corporation_id])->first();

                $character = UserToCorporation::where(['character_id' => $info->character_id])->first();

                $mail = new JobSendEveMail;
                $mail->sender = 93738489;
                $mail->subject = 'Market Taxes Owed';
                $mail->body = 'Year ' . $start->year . ' ' .
                            'Month: ' . 
                            $start->month .
                            '<br>Market Taxes Owed: ' .
                            number_format($finalTaxes, 2, '.', ',') .
                            '<br>Please remit to Spatial Forces';
                $mail->recipient = (int)$info->character_id;
                $mail->recipient_type = 'character';
                SendEveMailJob::dispatch($mail);
            }
            
        }
        
        //Mark the job as finished
        $task->SetStopStatus();
    }
}
