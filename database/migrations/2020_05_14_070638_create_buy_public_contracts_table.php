<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyPublicContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('eve_regions')) {
            Schema::create('eve_regions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('region_id');
                $table->string('region_name');
            });
        }

        if(!Schema::hasTable('public_contracts')) {
            Schema::create('public_contracts', function(Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('region_id');
                $table->decimal('buyout', 17,2 )->nullable();
                $table->decimal('collateral', 17, 2)->nullable();
                $table->unsignedInteger('contract_id');
                $table->dateTime('date_expired');
                $table->dateTime('date_issued');
                $table->unsignedInteger('days_to_complete')->nullable();
                $table->unsignedBigInteger('end_location_id')->nullable();
                $table->boolean('for_corporation')->nullable();
                $table->unsignedInteger('issuer_corporation_id');
                $table->unsignedInteger('issuer_id');
                $table->decimal('price', 17, 2)->nullable();
                $table->decimal('reward', 17, 2)->nullable();
                $table->unsignedBigInteger('start_location_id')->nullable();
                $table->string('title')->nullable();
                $table->enum('type', [
                    'unknown',
                    'item_exchange',
                    'auction',
                    'courier',
                    'loan',
                ]);
                $table->decimal('volume', 17,2);
            });
        }

        if(!Schema::hasTable('public_contract_items')) {
            Schema::create('public_contract_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('contract_id');
                $table->boolean('is_blueprint_copy')->nullable();
                $table->boolean('is_included');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->unsignedInteger('material_efficiency')->nullable();
                $table->unsignedInteger('quantity');
                $table->unsignedBigInteger('record_id');
                $table->unsignedInteger('runs')->nullable();
                $table->unsignedInteger('time_efficiency')->nullable();
                $table->unsignedBigInteger('type_id');
            });
        }

        if(!Schema::hasTable('market_region_orders')) {
            Schema::create('market_region_orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('region_id');
                $table->unsignedInteger('duration');
                $table->boolean('is_buy_order');
                $table->dateTime('issued');
                $table->unsignedInteger('location_id');
                $table->unsignedInteger('min_volume');
                $table->unsignedBigInteger('order_id');
                $table->decimal('price', 17, 2);
                $table->enum('range', [
                    'station',
                    'region',
                    'solarsystem',
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                    '10',
                    '20',
                    '30',
                    '40',
                ]);
                $table->unsignedInteger('system_id');
                $table->unsignedInteger('type_id');
                $table->unsignedInteger('volume_remain');
                $table->unsignedInteger('volume_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_contracts');
        Schema::dropIfExists('public_contract_items');
        Schema::dropIfExists('market_groups');
        Schema::dropIfExists('market_prices');
        Schema::dropIfExists('market_region_orders');
        Schema::dropIfExists('alliance_wormholes');
        Schema::dropIfExists('wormhole_types');
    }
}
