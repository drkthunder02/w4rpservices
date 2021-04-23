<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Helpers;

//Internal Library
use Log;
use Carbon\Carbon;
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;

//Application Library
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\Finances\AllianceWalletJournal;

class FinanceHelper {

    public function GetApiWalletJournal($division, $charId) {
        //Declare class variables
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Setup the esi container.
        $token = $esiHelper->GetRefreshToken($charId);
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Check the scope
        if(!$esiHelper->HaveEsiScope($charId, 'esi-wallet.read_corporation_wallets.v1')) {
            Log::critical('Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }

        //Reference the character id to the corporation id
        $char = $lookup->GetCharacterInfo($charId);
        $corpId = $char->corporation_id;

        //Set the current page to 1 which is the page we start on
        $currentPage = 1;
        //Set the total pages to 1, but in the future we will set it to another number
        $totalPages = 1;
        //Setup a page failed variable
        $pageFailed = false;

        do {
            /**
             * During the course of the operation, we want to ensure our token hasn't expired.
             * If the token has expired, then resetup the authentication container, which will refresh the
             * access token.
             */
            if($esiHelper->TokenExpired($token)) {
                $token = $esiHelper->GetRefreshToken($charId);
                $esi = $esiHelper->SetupEsiAuthentication($token);
            }

            /**
             * Attempt to get the data from the esi api.  If it fails, we skip the page, and go onto the next page, unless
             * the failed page is the first page.
             */
            try {
                $journals = $esi->page($currentPage)
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                                    'corporation_id' => $corpId,
                                    'division' => $division,
                                ]);
            } catch(RequestFailedException $e) {
                Log::warning('Failed to get wallet journal page ' . $currentPage . ' for character id: ' . $charId);
                Log::warning($e);
                dd($e);
                $pageFailed = true;
            }

            /**
             * If the current page is the first one and the page didn't fail, then update the total pages.
             * If the first page failed, just return as we aren't going to be able to get the total amount of data needed.
             */
            if($currentPage == 1 && $pageFailed == false) {
                $totalPages = $journals->pages;
            } else if($currentPage == 1 && $pageFailed == true) {
                return null;
            }

            /**
             * If the page was successfully pulled, we need to decode the data, then cycle through the data, and save it
             * where we can.
             */
            if($pageFailed == false) {
                //Decode the json data, and return it as an array
                $wallet = json_decode($journals->raw, true);

                
                //Foreach journal entry, add the journal entry to the table
                foreach($wallet as $entry) {                 
                    //See if we find the entry id in the database already
                    $found = AllianceWalletJournal::where([
                        'id' => $entry['id'],
                    ])->count();

                    if($found == 0) {
                        $awj = new AllianceWalletJournal;
                        $awj->id = $entry['id'];
                        $awj->corporation_id = $corpId;
                        $awj->division = $division;
                        if(isset($entry['amount'])) {
                            $awj->amount = $entry['amount'];
                        }
                        if(isset($entry['balance'])) {
                            $awj->balance = $entry['balance'];
                        }
                        if(isset($entry['context_id'])) {
                            $awj->context_id = $entry['context_id'];
                        }
                        if(isset($entry['date'])) {
                            $awj->date = $esiHelper->DecodeDate($entry['date']);
                        }
                        if(isset($entry['description'])) {
                            $awj->description = $entry['description'];
                        }
                        if(isset($entry['first_party_id'])) {
                            $awj->first_party_id = $entry['first_party_id'];
                        }
                        if(isset($entry['reason'])) {
                            $awj->reason = $entry['reason'];
                        }
                        if(isset($entry['ref_type'])) {
                            $awj->ref_type = $entry['ref_type'];
                        }
                        if(isset($entry['tax'])) {
                            $awj->tax = $entry['tax'];
                        }
                        if(isset($entry['tax_receiver_id'])) {
                            $awj->tax_receiver_id = $entry['tax_receiver_id'];
                        }
                        $awj->save();

                    }
                }
            } else {
                /**
                 * If the current page failed to get data from the esi, then reset the page failed data.
                 * Continue to try to pull the next page of data.  We might be able to get the current failed page
                 * later in another pull if it is successful.
                 */
                $pageFailed = false;
            }

            //Increment the current page counter
            $currentPage++;
        } while($currentPage <= $totalPages);

        return 0;
    }

    /**
     * Get the pages for the alliance wallet journal
     */
    public function GetAllianceWalletJournalPages($division, $charId) {
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Setup the esi container.
        $token = $esiHelper->GetRefreshToken($charId);
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Check the scope
        if(!$esiHelper->HaveEsiScope($charId, 'esi-wallet.read_corporation_wallets.v1')) {
            Log::critical('Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }

        //Reference the character id to the corporation id
        $char = $lookup->GetCharacterInfo($charId);
        $corpId = $char->corporation_id;

        /**
         * Attempt to get the data from the esi api.  If it fails, we skip the page, and go onto the next page, unless
         * the failed page is the first page.
         */
        try {
            $journals = $esi->page(1)
                            ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                                'corporation_id' => $corpId,
                                'division' => $division,
                            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Failed to get wallet journal pages for character id: ' . $charId);
            Log::warning($e);
            return 0;
        }

        //Return the total pages
        return $journals->pages;
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
