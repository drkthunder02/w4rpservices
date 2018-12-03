<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFleetsActivityTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('fleets_activity_tracking')) {
            Schema::create('fleets_activity_tracking', function (Blueprint $table) {
                $table->increments('id');
                $table->string('fleetId');
                $table->string('character_id');
                $table->string('character_name')->nullable();
                $table->string('corporation_id');
                $table->string('corporation_name')->nullable();
                $table->string('region');
                $table->string('system');
                $table->string('ship');
                $table->string('ship_type')->nullable();
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
        Schema::dropIfExists('fleets_activity_tracking');
    }
}
