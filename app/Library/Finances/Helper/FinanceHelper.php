<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances\Helper;

use DB;
use App\Jobs\SendEveMail;

use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Mail\EveMail;

use App\Library\Esi\Esi;
use App\Library\Finances\MarketTax;
use App\Library\Finances\PlayerDonation;
use App\Library\Finances\ReprocessingTax;
use App\Library\Finances\JumpBridgeTax;
use App\Library\Finances\StructureIndustryTax;
use App\Library\Finances\OfficeFee;
use App\Library\Finances\PlanetProductionTax;
use App\Library\Finances\PISale;
use App\Library\Lookups\LookupHelper;

use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

class FinanceHelper {

    public function GetWalletTransaction($division, $charId) {
        //Declare the lookup class helper
        $lookups = new LookupHelper;
        
        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $tokenData = $this->TokenInfo($charId);
        $token = $tokenData['token'];
        $scope = $tokenData['scope'];

        //If the token is not found, send the user an eve mail, and just exit out of the function
        if($this->TokenNotFound($token, $scope)) {
            return null;
        }

        //Setup array for PI items
        $pi_items = [

        ];

        //Reference to see if the character is in our look up table for corporations and characters
        $corpId = $lookups->LookupCharacter($charId);

        //Create an ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Create the esi class varialble
        $esi = new Eseye($authentication);
        
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
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/transactions/', [
                    'corporation_id' => 98251577,
                    'division'  => 3,
                ]);
            } catch(RequestFailedException $e) {
                return $e->getEsiResponse();
            }

            //Set the total pages we need to cycle through.
            $totalPages = $journals->pages;
            //Decode the wallet from json into an array
            $wallet = json_decode($journals->raw, true);
            //For each journal entry, attempt to store it in the database.
            //The PutWalletJournal function checks to see if it's already in the database.
            foreach($wallet as $entry) {
                if($entry['amount'] > 0) {
                    if($division == 3 && $charId == 94415555) {
                        if(in_array($entry['type_id'], $pi_items, true)) {
                            $pi = new PISale();
                            $pi->InsertPISale($entry);
                        }
                    }
                }
                
            }
            
            //Increment the current page we are on.
            $currentPage++;
        //Continue looping through the do while loop until the current page is greater than or equal to the total pages.
        } while ($currentPage < $totalPages);
    }

    public function GetWalletJournal($division, $charId) {
        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $tokenData = $this->TokenInfo($charId);
        $token = $tokenData['token'];
        $scope = $tokenData['scope'];

        //Declare the lookup class helper
        $lookups = new LookupHelper;

        //If the token is not found, send the user an eve mail, and just exit out of the function
        if($this->TokenNotFound($token, $scope)) {
            return null;
        }
        
        //Reference to see if the character is in our look up table for corporations and characters
        $corpId = $lookups->LookupCharacter($charId);

        //Create an ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Create the esi class varialble
        $esi = new Eseye($authentication);
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
                return $e->getEsiResponse();
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
                        $market = new MarketTax();
                        $market->InsertMarketTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'reprocessing_tax') {
                        $reprocessing = new ReprocessingTax();
                        $reprocessing->InsertReprocessingTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'structure_gate_jump') {
                        $jb = new JumpBridgeTax();
                        $jb->InsertJumpBridgeTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'player_donation' ||
                                ($entry['ref_type'] == 'corporation_account_withdrawal' && $entry['second_party_id'] == 98287666)) {
                        $other = new PlayerDonation();
                        $other->InsertPlayerDonation($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'industry_job_tax' && $entry['second_party_id'] == 98287666) {
                        $industry = new StructureIndustryTax();
                        $industry->InsertStructureIndustryTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'office_rental_fee' && $entry['second_party_id'] == 98287666) {
                        $office = new OfficeFee();
                        $office->InsertOfficeFee($entry, $corpId, $division);
                    }
                }
            }
            
            //Increment the current page we are on.
            $currentPage++;
        //Continue looping through the do while loop until the current page is greater than or equal to the total pages.
        } while ($currentPage < $totalPages);
    }

    public function GetHoldingWalletJournal($division) {
        //Get the ESI refresh token for the corporation to add new wallet journals into the database
        $token = EsiToken::where(['character_id' => 93738489])->get(['refresh_token']);

        $corpId = 98287666;

        //Create an ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Create the esi class varialble
        $esi = new Eseye($authentication);
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
                return $e->getEsiResponse();
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
                        $market = new MarketTax();
                        $market->InsertMarketTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'reprocessing_tax') {
                        $reprocessing = new ReprocessingTax();
                        $reprocessing->InsertReprocessingTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'structure_gate_jump') {
                        $jb = new JumpBridgeTax();
                        $jb->InsertJumpBridgeTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'player_donation' ||
                              $entry['ref_type'] == 'corporation_account_withdrawal') {
                        $other = new PlayerDonation();
                        $other->InsertPlayerDonation($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'industry_job_tax') {
                        $industry = new StructureIndustryTax();
                        $industry->InsertStructureIndustryTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'planetary_import_tax' || $entry['ref_type'] == 'planetary_export_tax') {
                        $pi = new PlanetProductionTax();
                        $pi->InsertPlanetProductionTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'office_rental_fee') {
                        $office = new OfficeFee();
                        $office->InsertOfficeFee($entry, $corpId, $division);
                    }
                }
                
            }
            
            //Increment the current page we are on.
            $currentPage++;
        //Continue looping through the do while loop until the current page is greater than or equal to the total pages.
        } while ($currentPage < $totalPages);
    }

    private function TokenInfo($charId) {
        //Get the ESI refresh token for the corporation to add a new wallet jouranls into the database
        //send the token and scope back to the calling function
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
        $scope = EsiScope::where(['character_id' => $charId, 'scope' => 'esi-wallet.read_corporation_wallets.v1'])->get(['scope']);

        $data = [
            'token' => $token,
            'scope' => $scope,
        ];

        return $data;
    }

    private function TokenNotFound($token, $scope) {
        if(!isset($token[0]->refresh_token) || !isset($scope[0]->scope)) {
            //Register a mail to be dispatched as a job
            $mail = new EveMail;
            $mail->sender = 93738489;
            $mail->subject = 'W4RP Services ESI API';
            $mail->body = 'You need to register an ESI API on the services site for esi-wallet.read_corporation_wallet.v1<br>This is also labeled Corporation Wallets';
            $mail->recipient = (int)$charId;
            $mail->recipient_type = 'character';
            $mail->save();

            SendEveMail::dispatch($mail);

            return true;
        } 

        return false;
    }

}

?>
