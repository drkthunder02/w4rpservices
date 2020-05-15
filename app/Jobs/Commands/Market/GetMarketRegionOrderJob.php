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

class GetMarketRegionOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Private variables
     */
    private $esi;
    private $region;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($region, $esi = null)
    {
        //Setup the region variable
        $this->region = $region;
        //Setup the esi variable
        if($esi == null) {
            $this->esi = new Esi();
        } else {
            $this->esi = $esi;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get the market orders for a region
        $orders = $this->esi->invoke('get', '/markets/{region_id}/orders/', [
            'region_id' => $this->region,
        ]);

        foreach($orders as $order) {
            $count = MarketRegionOrder::where([
                'order_id',
            ])->count();

            if($count == 0) {
                $newOrder = new MarketRegionOrder;
                $newOrder->region_id = $this->region;
                $newOrder->duration = $order->duration;
                $newOrder->is_buy_order = $order->is_buy_order;
                $newOrder->issued = $order->issued;
                $newOrder->location_id = $order->location_id;
                $newOrder->min_volume = $order->min_volume;
                $newOrder->order_id = $order->order_id;
                $newOrder->price = $order->price;
                $newOrder->range = $order->range;
                $newOrder->system_id = $order->system_id;
                $newOrder->type_id = $order->type_id;
                $newOrder->volume_remain = $order->volume_remain;
                $newOrder->volume_total = $order->volume_total;
                $newOrder->save();
            } else if ($order->volume_remain == 0) {
                MarketRegionOrder::where([
                    'order_id' => $order->order_id,
                ])->delete();
            } else {
                MarketRegionOrder::where([
                    'order_id' => $order->order_id,
                ])->update([
                    'region_id' => $this->region,
                    'duration' => $order->duration,
                    'is_buy_order' => $order->is_buy_order,
                    'issued' => $order->issued,
                    'location_id' => $order->location_id,
                    'min_volume' => $order->min_volume,
                    'order_id' => $order->order_id,
                    'price' => $order->price,
                    'range' => $order->range,
                    'system_id' => $order->system_id,
                    'type_id' => $order->type_id,
                    'volume_remain' => $order->volume_remain,
                    'volume_total' => $order->volume_total,
                ]);
            }
        }
    }
}
