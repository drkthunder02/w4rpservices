<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contracts')) {
            Schema::create('contracts', function(Blueprint $table) {
                $table->increments('contract_id')->unique();
                $table->string('title');
                $table->date('end_date');
                $table->text('body');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('contract_bids')) {
            Schema::create('contract_bids', function(Blueprint $table) {
                $table->increments('id')->unique();
                $table->integer('contract_id');
                $table->decimal('bid_amount');
                $table->string('character_name');
                $table->string('character_id');
                $table->string('corporation_name');
                $table->string('corporation_id');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('accepted_bid')) {
            Schema::create('accepted_bid', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('contract_id');
                $table->integer('bid_id');
                $table->decimal('amount');
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
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_bids');
        Schema::dropIfExists('contract_bid_accepted');
    }
}
