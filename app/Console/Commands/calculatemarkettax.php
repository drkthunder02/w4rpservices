<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;
use App\Library\Structures\StructureTaxHelper;
use App\Library\Esi\Esi;

use App\Models\Market\MonthlyMarketTax;
use App\Models\ScheduledTask\ScheduleJob;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Corporation\CorpStructure;



class CalculateMarketTax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:calculatemarkettax';

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
        $corps = CorpStructure::where(['structure_type' => 'Citadel'])->distinct('corporation_id');
        
        foreach($corps as $corp) {
            $finalTaxes = $sHelper->GetTaxes($corp->corporation_id, 'Market', $start, $end);

            if($finalTaxes < 0.00) {
                $finalTaxes = 0.00;
            }
            
            $finalTaxes = number_format($finalTaxes, 2, '.', ',');
            $this->line('Final Taxes are: ' . $finalTaxes);

            //Get the info about the structures from the database
            $info = CorpStructure::where(['corporation_id' => $corp->corporation_id])->first();
            //Store the value in the database
            $bill = new MonthlyMarketTax;
            $bill->character_id = $info->character_id;
            $bill->character_name = $info->character_name;
            $bill->corporation_id = $corp->corporation_id;
            $bill->corporation_name = $info->corporation_name;
            $bill->taxed_owed = $finalTaxes;
            $bill->month = $start->monthName;
            $bill->year = $start->year;
            $bill->save();
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
