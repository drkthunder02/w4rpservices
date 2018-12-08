<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Commands\Library\CommandHelper;

use App\Models\Market\MonthlyMarketTax;
use App\Models\ScheduledTask\ScheduleJob;
use App\Models\Corporation\CorpJournal;
use App\Models\Corporation\CorpStructure;

use App\Library\FinanceHelper;

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
        $start = Carbon::now()->startOfMonth()->subMonth();
        $end = Carbon::now()->endOfMOnth()->subMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        //Get the set of corporations from the structure table
        $corps = CorpStructure::where(['structure_type' => 'Citadel'])->distinct('corporation_id');
        
        foreach($corps as $corp) {
            //Get the number of citadel counts to use in fuel block calculation
            $citadelCount = CorpStructure::where(['corporation_id' => $corp->corporation_id, 'structure_type' => 'Citadel'])->count();
            //From the corp journal add up the taxes from the last month
            $marketTaxes = CorpJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corp->corporation_id])
                                        ->whereBetween('date', [$start, $end])
                                        ->sum('amount');
            //Calculate the market fuel cost
            $marketFuel = $hFinances->CalculateFuelBlockCost('market');
            //Calculate the market tax
            $mTax = CorpStructure::where(['corporation_id' => $corp->corporation_id, 'structure_type' => 'Citadel'])->avg('tax');
            //Subtract the fuel cost from the market taxes
            $tempTaxes = $marketTaxes - ($marketFuel * $citadelCount);
            //Calculate the final tax in order to store in the database
            $finalTaxes = $hFinancees->CalculateTax($tempTaxes, $mTax, 'market');
            //Check for a negative number and zero out if negative
            if($finalTaxes < 0.00) {
                $finalTaxes = 0.00;
            } else {
                $finalTaxes = number_format($finalTaxes, 2, '.', ',');
            }
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
