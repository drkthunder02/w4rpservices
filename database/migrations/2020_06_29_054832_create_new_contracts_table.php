<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Remove this group of tables
        Schema::dropIfExists('eve_regions');
        Schema::dropIfExists('public_contracts');
        Schema::dropIfExists('public_contract_items');
        Schema::dropIfExists('market_region_orders');
        Schema::dropIfExists('market_groups');
        Schema::dropIfExists('market_prices');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_bids');
        Schema::dropIfExists('accepted_bids');

        //Add these new tables for the contracts
        if(!Schema::hasTable('supply_chain_contracts')) {
            Schema::create('supply_chain_contracts', function(Blueprint $table) {
                $table->increments('contract_id')->unique();
                $table->unsignedBigInteger('issuer_id');
                $table->string('issuer_name');
                $table->string('title');
                $table->dateTime('end_date');
                $table->dateTime('delivery_by');
                $table->text('body')->nullable();
                $table->enum('state', [
                    'open',
                    'closed',
                    'completed',
                ]);
                $table->unsignedInteger('bids')->default(0);
                $table->decimal('final_cost', 20, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('supply_chain_bids')) {
            Schema::create('supply_chain_bids', function(Blueprint $table) {
                $table->increments('bid_id')->unique();
                $table->unsignedBigInteger('contract_id');
                $table->decimal('bid_amount', 20, 2)->default(0.00);
                $table->unsignedBigInteger('entity_id');
                $table->string('entity_name')->nullable();
                $table->enum('entity_type', [
                    'character',
                    'corporation',
                    'alliance',
                ]);
                $table->enum('bid_type', [
                    'accepted',
                    'pending',
                    'not_accepted',
                ]);
                $table->text('bid_note');
                $table->timestamps();
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
        Schema::dropIfExists('supply_chain_contract');
        Schema::dropIfExists('supply_chain_bids');
    }
}
