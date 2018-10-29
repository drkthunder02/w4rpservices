<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MarketOrders', function(Blueprint $table) {
            $table->integer('duration');
            $table->boolean('is_buy_order');
            $table->dateTime('issued');
            $table->integer('location_id');
            $table->integer('min_volume');
            $table->integer('order_id')->unique();
            $table->decimal('price', 20, 2);
            $table->string('range');
            $table->integer('system_id');
            $table->integer('volume_remain');
            $table->integer('volume_total');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MarketOrders');
    }
}
