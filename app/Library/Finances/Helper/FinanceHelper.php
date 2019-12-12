<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances\Helper;

//Internal Library
use DB;
use Log;

//Job
use App\Jobs\ProcessSendEveMailJob;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Mail\EveMail;

//Library
use App\Library\Esi\Esi;
use App\Library\Finances\AllianceMarketTax;
use App\Library\Finances\CorpMarketTax;
use App\Library\Finances\MarketTax;
use App\Library\Finances\PlayerDonation;
use App\Library\Finances\ReprocessingTax;
use App\Library\Finances\JumpBridgeTax;
use App\Library\Finances\StructureIndustryTax;
use App\Library\Finances\OfficeFee;
use App\Library\Finances\PlanetProductionTax;
use App\Library\Finances\PISale;
use App\Library\Lookups\NewLookupHelper;

//Seat Stuff
use Seat\Eseye\Exceptions\RequestFailedException;


class FinanceHelper {

    public function GetWalletJournal($division, $charId) {
        //Declare new class variables
        $market = new MarketTax();
        $reprocessing = new ReprocessingTax();
        $jb = new JumpBridgeTax();
        $other = new PlayerDonation();
        $industry = new StructureIndustryTax();
        $office = new OfficeFee();
        $esiHelper = new Esi();
        $lookup = new NewLookupHelper;

        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $hasScope = $esiHelper->HaveEsiScope($charId, 'esi-wallet.read_corporation_wallets.v1');
        if($hasScope == false) {
            Log::critical('Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }
        $token = $esiHelper->GetRefreshToken($charId);
        if($token == null) {
            return null;
        }        
        
        //Reference to see if the character is in our look up table for corporations and characters
        $corpId = $lookup->LookupCharacter($charId, null);

        //Create an ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        //Set the version
        $esi->setVersion('v4');
        
        //Set our current page to 1 which is the one we are starting on.
        $currentPage = 1;
        //Set our default total pages to 1 in case our try section fails out.
        $totalPages = 1;

        //If more than one page is found, decode the first set of wallet entries, then call for the next pages
        do {
            //Call the first page of the wallet journal, as we are always going to get at least one page.
            //If we have more pages, then we will continue through the while loop.
            try {
                $journals = $esi->page($currentPage)
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                    'corporation_id' => $corpId,
                    'division'  => $division,
                ]);
            } catch(RequestFailedException $e) {
                return null;
            }

            //Set the total pages we need to cycle through.
            $totalPages = $journals->pages;
            //Decode the wallet from json into an array
            $wallet = json_decode($journals->raw, true);
            //For each journal entry, attempt to store it in the database.
            //The PutWalletJournal function checks to see if it's already in the database.
            foreach($wallet as $entry) {
                if($entry['amount'] > 0) {
                    if($entry['ref_type'] == 'brokers_fee') {
                        $market->InsertMarketTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'reprocessing_tax') {
                        $reprocessing->InsertReprocessingTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'structure_gate_jump') {
                        $jb->InsertJumpBridgeTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'player_donation' ||
                             ($entry['ref_type'] == 'corporation_account_withdrawal' && $entry['second_party_id'] == 98287666)) {
                        $other->InsertPlayerDonation($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'industry_job_tax' && $entry['second_party_id'] == 98287666) {
                        $industry->InsertStructureIndustryTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'office_rental_fee' && $entry['second_party_id'] == 98287666) {
                        $office->InsertOfficeFee($entry, $corpId, $division);
                    }
                }
            }
            
            //Increment the current page we are on.
            $currentPage++;
        //Continue looping through the do while loop until the current page is greater than or equal to the total pages.
        } while ($currentPage < $totalPages);
    }

    public function GetJournalPageCount($division, $charId) {
        //Declare class variables
        $lookup = new NewLookupHelper;
        $esiHelper = new Esi;
        
        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $hasScope = $esiHelper->HaveEsiScope($charId, 'esi-wallet.read_corporation_wallets.v1');
        if($hasScope == false) {
            Log::critical('Esi Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }
        $token = $esiHelper->GetRefreshToken($charId);
        if($token == null) {
            return null;
        }

        //Refrence to see if the character is in our look up table for corporation and characters
        $corpId = $lookup->LookupCharacter($charId, null);

        //Create the ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Set the esi version to v4
        $esi->setVersion('v4');

        //Call the first page so we can get the header data for the number of pages
        try {
            $journals = $esi->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                'corporation_id' => $corpId,
                'division'  => $division,
            ]);
        } catch(RequestFailedException $e) {
            //Log::warning($e->getEsiResponse());
            return null;
        }

        $pages = $journals->pages;

        return $pages;
    }

    public function GetCorpWalletJournalPage($division, $charId, $corpId, $page = 1) {
        //Declare new class variables
        $corpMarket = new MarketTax();
        $esiHelper = new Esi;

        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $token = $esiHelper->GetRefreshToken($charId);

        //Setup the esi authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        $esi->setVersion('v4');

        //Call the page of the wallet journal
        try {
            $journals = $esi->page($page)
                            ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                                'corporation_id' => $corpId,
                                'division' => $division,
                            ]);
        } catch(RequestFailedException $e) {
            Log::warning($e->getEsiResponse());
            return null;
        }

        //Decode the wallet from json into an array
        $wallets = json_decode($journals->raw, true);

        //For each journal entry, attempt to store the information into the database
        foreach($wallets as $wallet) {
            if($wallet['amount'] > 0) {
                if($wallet['ref_type'] == 'brokers_fee') {
                    $corpMarket->InsertCorpMarketTax($wallet, $corpId, $division);
                }
            }
        }
    }

    public function GetWalletJournalPage($division, $charId, $page = 1) {
        //Declare new class variables
        $market = new AllianceMarketTax;
        $reprocessing = new ReprocessingTax;
        $jb = new JumpBridgeTax;
        $other = new PlayerDonation;
        $industry = new StructureIndustryTax;
        $office = new OfficeFee;
        $pi = new PlanetProductionTax;
        $esiHelper = new Esi;
        $lookup = new NewLookupHelper;

        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $hasScope = $esiHelper->HaveEsiScope($charId, 'esi-wallet.read_corporation_wallets.v1');
        if($hasScope == false) {
            Log::critical('Esi Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }
        $token = $esiHelper->GetRefreshToken($charId);
        if($token == null) {
            return null;
        }       
        
        //Reference to see if the character is in our look up table for corporations and characters
        $char = $lookup->LookupCharacter($charId, null);
        $corpId = $char->corporation_id;

        //Create an ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        $esi->setVersion('v4');

        //Call the first page of the wallet journal, as we are always going to get at least one page.
        //If we have more pages, then we will continue through the while loop.
        try {
            $journals = $esi->page($page)
                            ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                'corporation_id' => $corpId,
                'division'  => $division,
            ]);
        } catch(RequestFailedException $e) {
            //Log::warning($e->getEsiResponse());
            return null;
        }

        //Decode the wallet from json into an array
        $wallet = json_decode($journals->raw, true);
        //For each journal entry, attempt to store it in the database.
        //The PutWalletJournal function checks to see if it's already in the database.
        foreach($wallet as $entry) {
            if($entry['amount'] > 0) {
                if($entry['ref_type'] == 'brokers_fee') {
                    $market->InsertMarketTax($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'reprocessing_tax') {
                    $reprocessing->InsertReprocessingTax($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'structure_gate_jump') {
                    $jb->InsertJumpBridgeTax($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'player_donation' ||
                         ($entry['ref_type'] == 'corporation_account_withdrawal' && $entry['second_party_id'] == 98287666)) {
                    $other->InsertPlayerDonation($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'industry_job_tax' && $entry['second_party_id'] == 98287666) {
                    $industry->InsertStructureIndustryTax($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'office_rental_fee' && $entry['second_party_id'] == 98287666) {
                    $office->InsertOfficeFee($entry, $corpId, $division);
                } else if($entry['ref_type'] == 'planetary_export_tax' || $entry['ref_type'] == 'planetary_import_tax') {
                    $pi->InsertPlanetProductionTax($entry, $corpId, $division);
                }
            }
        }
    }

    private function GetPIMaterialsArray() {
        //Setup array for PI items
        $pi_items = [
            //R0 Materials
            '2073',
            '2667',
            '2268',
            '2270',
            '2272',
            '2286',
            '2287',
            '2288',
            '2305',
            '2306',
            '2307',
            '2308',
            '2309',
            '2310',
            '2311',
            //P1 Materials
            '2389',
            '2390',
            '2392',
            '2393',
            '2395',
            '2396',
            '2397',
            '2398',
            '2399',
            '2400',
            '2401',
            '3645',
            '3683',
            '3779',
            '9828',
            //P2 Materials
            '44',
            '2312',
            '2317',
            '2319',
            '2321',
            '2327',
            '2328',
            '2329',
            '2463',
            '3689',
            '3691',
            '3693',
            '3695',
            '3697',
            '3725',
            '3775',
            '3828',
            '9830',
            '9832',
            '9836',
            '9838',
            '9840',
            '9842',
            '15317',
            //P3 Materials
            '2344',
            '2345',
            '2346',
            '2348',
            '2349',
            '2351',
            '2352',
            '2354',
            '2358',
            '2360',
            '2361',
            '2366',
            '2367',
            '9834',
            '9846',
            '9848',
            '12836',
            '17136',
            '17392',
            '17898',
            '28974',
            //P4 Materials
            '2867',
            '2868',
            '2869',
            '2870',
            '2871',
            '2872',
            '2875',
            '2876',
        ];

        return $pi_items;
    }

}

?>
