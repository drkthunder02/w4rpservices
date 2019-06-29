<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrpTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('srp_ships')) {
            Schema::create('srp_ships', function (Blueprint $table) {
                $table->increments('id');
                $table->string('ship_type');
                $table->string('character_id');
                $table->string('zkillboard');
                $table->string('loss_values');
                $table->text('notes');
            });
        }

        if(!Schema::hasTable('srp_fleets')) {
            Schema::create('srp_fleets', function(Blueprint $table) {
                $table->increments('fleet_id');
                $table->string('fleet_name');
                $table->string('fleet_commander');
                $table->string('fleet_commander_id');
                $table->string('fleet_type');
                $table->string('fleet_description');
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
        Schema::dropIfExists('srp_ships');
        Schema::dropIfExists('srp_fleets');
    }
}
