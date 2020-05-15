<?php

namespace App\Jobs\Commands\Eve;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\Eve\EveRegion;

class GetEveRegionsJob implements ShouldQueue
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
        $esi = new Esi();

        $regions = $esi->invoke('get', '/universe/regions/');
        $responses = $esi->setBody($regions)->invoke('post', '/universe/names/');

        foreach($responses as $resp) {
            if($resp->category == 'region') {
                EveRegion::insertOrIgnore([
                    'region_id' => $resp->id,
                    'region_name' => $resp->name,
                ]);
            }
        }

    }
}
