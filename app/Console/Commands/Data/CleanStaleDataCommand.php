<?php

namespace App\Console\Commands\Data;

//Internal Library
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;

//Models
use App\Models\Lookups\AllianceLookup;
use App\Models\Lookups\CharacterLookup;
use App\Models\Lookups\CorporationLookup;
use App\Models\Lookups\ItemLookup;
use App\Models\Finances\AllianceMarketJournal;
use App\Models\Finances\JumpBridgeJournal;
use App\Models\Finances\OfficeFeesJournal;
use App\Models\Finances\PISaleJournal;
use App\Models\Finances\PlanetProductionTaxJournal;
use App\Models\Finances\ReprocessingTaxJournal;
use App\Models\Finances\SovBillJournal;
use App\Models\Finances\StructureIndustryTaxJournal;

class CleanStaleDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:CleanData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old database data';

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
        //Empty the item lookup table
        ItemLookup::truncate();

        //Empty the character lookup table
        CharacterLookup::truncate();

        //Empty the corporation lookup table
        CorporationLookup::truncate();

        //Empty the alliance lookup table
        AllianceLookup::truncate();

        //Setup today's carbon date
        $today = Carbon::now();
        $ago = $today->subMonths(6);

        //Clean old data from the Alliance Market Tax Journal
        $markets = AllianceMarketJournal::all();
        foreach($markets as $market) {
            $date = new Carbon($market->created_at);
            if($date->lessThan($ago)) {
                AllianceMarketJournal::where([
                    'id' => $market->id,
                ])->delete();
            }
        }

        //Clean old data from Jump Bridge Journal
        $jumps = JumpBridgeJournal::all();
        foreach($jumps as $jump) {
            $date = new Carbon($jump->created_at);
            if($date->lessThan($ago)) {
                JumpBridgeJournal::where([
                    'id' => $jump->id,
                ])->delete();
            }
        }

        //Clean old data from office fees journal
        $offices = OfficeFeesJournal::all();
        foreach($offices as $office) {
            $date = new Carbon($office->created_at);
            if($date->lessThan($ago)) {
                OfficeFeesJournal::where([
                    'id' => $office->id,
                ])->delete();
            }
        }

        //Clean old data from pi sale journal
        $pisales = PISaleJournal::all();
        foreach($pisales as $sale) {
            $date = new Carbon($sale->created_at);
            if($date->lessThan($ago)) {
                PISaleJournal::where([
                    'id' => $sale->id,
                ])->delete();
            }
        }

        //Clean old data from planet production tax journal
        $pis = PlanetProductionTaxJournal::all();
        foreach($pis as $pi) {
            $date = new Carbon($pi->created_at);
            if($date->lessThan($ago)) {
                PlanetProductionTaxJournal::where([
                    'id' => $pi->id,
                ])->delete();
            }
        }

        //Clean old data from player donation journal
        $donations = PlayerDonationJournal::all();
        foreach($donations as $donation) {
            $date = new Carbon($donation->created_at);
            if($date->lessThan($ago)) {
                PlayerDonationJournal::where([
                    'id' => $donation->id,
                ])->delete();
            }
        }

        //Clean old data from Reprocessing Tax Journal
        $reps = ReprocessingTaxJournal::all();
        foreach($reps as $rep) {
            $date = new Carbon($rep->created_at);
            if($date->lessThan($ago)) {
                ReprocessingTaxJournal::where([
                    'id' => $rep->id,
                ])->delete();
            }
        }

        //Clean old sov bill journal data
        $sovs = SovBillJournal::all();
        foreach($sovs as $sov) {
            $date = new Carbon($sov->created_at);
            if($date->lessThan($ago)) {
                SovBillJournal::where([
                    'id' => $sov->id,
                ])->delete();
            }
        }

        //Clean old structure industry tax journal data
        $industrys = StructureIndustryTaxJournal::all();
        foreach($industrys as $indy) {
            $date = new Carbon($indy->created_at);
            if($date->lessThan($ago)) {
                StructureIndustryTaxJournal::where([
                    'id' => $indy->id,
                ])->delete();
            }
        }
    }
}
