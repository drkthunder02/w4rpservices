<?php

namespace App\Jobs\Commands\Market;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Log;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

class GetMarketGroupsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Setup the esi authentication container
        $esi = new Esi();

        $groups = $esi->invoke('get', '/markets/groups/');

        foreach($groups as $group) {
            $grpResponse = $esi->invoke('get', '/markets/groups/{market_group_id}/', [
                'market_group_id' => $group,
            ]);

            foreach($grpResponse->types as $type) {
                MarketGroup::insertOrIgnore([
                    'group' => $group,
                    'description' => $grpResponse->description,
                    'market_group_id' => $grpResponse->market_group_id,
                    'name' => $grpResponse->name,
                    'parent_group_id' => $grpResponse->parent_group_id,
                    'type' => $type,
                ]);
            }
        }
    }
}
