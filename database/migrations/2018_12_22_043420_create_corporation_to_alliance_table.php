<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationToAllianceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('corporation_to_alliance')) {
            Schema::create('corporation_to_alliance', function (Blueprint $table) {
                $table->increments('id');
                $table->string('corporation_id');
                $table->string('corporation_name');
                $table->string('alliance_id');
                $table->string('alliance_name');
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
        Schema::dropIfExists('corporation_to_alliance');
    }
}
