<?php

namespace App\Library\Contracts;

//Internal Library
use Log;
use DB;

//App Library
use App\Jobs\Library\JobHelper;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\Jobs\JobProcessContracts;
use App\Models\Job\JobStatus;
use App\Models\Logistics\Contract;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class EveContractsHelper {

    private $charId;
    private $corpId;
    private $page;

    public function __construct($char, $corp, $pg = null) {
        $this->charId = $char;
        $this->corpId = $corp;
        $this->page = $pg;
    }

    /**
     * Get a page of Contracts to store in the database
     */
    public function GetContractsByPage() {
        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id' => $this->charId])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Setup the ESI variable
        $esi = new Eseye($authentication);

        try {
            $contracts = $esi->page($this->page)
                             ->invoke('get', '/corporations/{corporation_id}/contracts/', [
                                 'corporation_id' => $this->corpId,
                             ]);
        } catch(RequestFailedException $e) {
            Log::critical("Failed to get a page of contracts from ESI.");
            $contracts = null;
        }

        return $contracts;
    }

    /**
     * Store a new contract record in the database
     */
    public function StoreNewContract($contract) {
        //Declare esi helper for decoding the date
        $esiHelper = new Esi;

        //Setup the esi authentication container
        $config = config('esi');
        //Get the refresh token from the database
        $token = EsiToken::where(['character_id', $this->charId])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Setup the ESI variable
        $esi = new Eseye($authentication);

        //See if we find the contract in the database
        $found = LogisticsContract::where([
            'contract_id' => $contract->contract_id,
        ])->count();
        //If nothing is found we need to store the contract
        if($found == 0) {
            $logi = new LogisticsContract;
            $logi->acceptor_id = $contract->acceptor_id;
            $logi->assignee_id = $contract->assignee_id;
            $logi->availability = $contract->availability;
            if(isset($contract->buyout)) {
                $logi->buyout = $contract->buyout;
            }
            if(isset($contract->collateral)) {
                $logi->collateral = $contract->collateral;
            }
            $logi->contract_id = $contract->contract_id;
            if(isset($contract->date_accepted)) {
                $logi->date_accepted = $esiHelper->DecodeDate($contract->date_accepted);
            }
            if(isset($contract->date_completed)) {
                $logi->date_completed = $esiHelper->DecodeDate($contract->date_completed);
            }
            $logi->date_expired = $contract->date_expired;
            $logi->date_issued = $contract->date_issued;
            if(isset($contract->days_to_complete)) {
                $logi->days_to_complete = $contract->days_to_complete;
            }
            if(isset($contract->end_location_id)) {
                $logi->end_location_id = $contract->end_location_id;
            }
            $logi->for_corporation = $contract->for_corporation;
            $logi->issuer_corporation_id = $contract->issuer_corporation_id;
            $logi->issuer_id = $contract->issuer_id;
            if(isset($contract->price)) {
                $logi->price = $contract->price;
            }
            if(isset($contract->reward)) {
                $logi->reward = $contract->reward;
            }
            if(isset($contract->start_location_id)) {
                $logi->start_location_id = $contract->start_location_id;
            }
            $logi->status = $contract->status;
            if(isset($contract->title)) {
                $logi->title = $contract->title;
            }
            $logi->type = $contract->type;
            $logi->status = $contract->status;
            if(isset($contract->volume)) {
                $logi->volume = $contract->volume;
            }
            $logi->save();
        } else { //If the contract is found, then call the function to update the contract
            $this->UpdateLogisticsContract($contract);
        }
    }

    public function UpdateLogisticsContract($contract) {
        //Declare Esi Helper function
        $esiHelper = new Esi;

        LogisticsContract::where([
            'contract_id' => $contract->contract_id,
        ])->update([
            'acceptor_id' => $contract->acceptor_id,
            'assignee_id' => $contract->assignee_id,
            'availability' => $contract->availability,
            'date_expired' => $esiHelper->DecodeDate($contract->date_expired),
            'date_issued' => $esiHelper->DecodeDate($contract->date_issued),
            'for_corporation' => $contract->for_corporation,
            'issuer_corporation_id' => $contract->issuer_corporation_id,
            'issuer_id' => $contract->issuer_id,
            'status' => $contract->status,
            'type' => $contract->type,
        ]);

        if(isset($contract->buyout)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'buyout' => $contract->buyout,
            ]);
        }

        if(isset($contract->collateral)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'collateral' => $contract->collateral,
            ]);
        }

        if(isset($contract->date_accepted)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'date_accepted' => $esiHelper->DecodeDate($contract->date_accepted),
            ]);
        }

        if(isset($contract->date_completed)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'date_completed' => $esiHelper->DecodeDate($contract->date_completed),
            ]);
        }

        if(isset($contract->days_to_complete)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'days_to_complete' => $contract->days_to_complete,
            ]);
        }

        if(isset($contract->end_location_id)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'end_location_id' => $contract->end_location_id,
            ]);
        }

        if(isset($contract->price)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'price' => $contract->price,
            ]);
        }

        if(isset($contract->reward)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'reward' => $contract->reward,
            ]);
        }

        if(isset($contract->start_location_id)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'start_location_id' => $contract->start_location_id,
            ]);
        }

        if(isset($contract->title)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'title' => $contract->title,
            ]);
        }

        if(isset($contract->volume)) {
            LogisticsContract::where([
                'contract_id' => $contract->contract_id,
            ])->update([
                'volume' => $contract->voluem,
            ]);
        }
    }

    public function PurgeOldContracts() {
        $date = Carbon::now();

        LogisticsContract::where('date_expired', '<', $date)->delete();
    }

}

?>