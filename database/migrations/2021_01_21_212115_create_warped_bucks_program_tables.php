<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarpedBucksProgramTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bucks_character_wallet_entries', function(Blueprint $table) {
            $table->id();
        });

        Schema::create('bucks_alliance_systems', function(Blueprint $table) {
            $table->id();
        });

        Schema::create('bucks_ratting_daily_pool', function(Blueprint $table) {
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bucks_character_wallet_entries');
        Schema::dropIfExists('bucks_alliance_systems');
        Schema::dropIfExists('bucks_ratting_daily_pool');
    }
}
