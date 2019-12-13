<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNonUtilizedDbTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_to_corporation');
        Schema::dropIfExists('character_to_corporation');
        Schema::dropIfExists('corporation_to_alliance');
        Schema::dropIfExists('corp_market_structures');
        Schema::dropIfExists('eve_contracts');
        Schema::dropIfExists('logistics_contracts');
        Schema::dropIfExists('logistics_insurance_deposits');
        Schema::dropIfExists('logistics_insurance_payouts');
        Schema::dropIfExists('logistics_routes');
        Schema::dropIfExists('solar_system_distances');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
