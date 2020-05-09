<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentalMoonLedgerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_mining_observers')) {
            Schema::create('alliance_mining_observers', function(Blueprint $table) {
                $table->unsignedBigIncrements('id');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->unsignedBigInteger('observer_id');
                $table->string('observer_name');
                $table->string('observer_type');
                $table->dateTime('last_updated');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_rental_moon_ledgers')) {
            Schema::create('alliance_rental_moon_ledgers', function(Blueprint $table) {
                $table->unsignedBigIncrements('id');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->unsignedBigInteger('character_id');
                $table->string('character_name');
                $table->unsignedBigInteger('observer_id');
                $table->string('observer_name');
                $table->unsignedBigInteger('type_id');
                $table->string('ore');
                $table->unsignedBigInteger('quantity');
                $table->unsignedBigInteger('recorded_corporation_id');
                $table->string('record_corporation_name');
                $table->dateTime('last_updated');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('corp_mining_observers')) {
            Schema::create('corp_mining_observers', function(Blueprint $table) {
                $table->unsignedBigIncrements('id');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->unsignedBigInteger('observer_id');
                $table->string('observer_name');
                $table->string('observer_type');
                $table->unsignedBigInteger('observer_owner_id');
                $table->unsignedBigInteger('solar_system_id');
                $table->unsignedBigInteger('observer_type_id');
                $table->dateTime('last_updated');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('corp_moon_ledgers')) {
            Schema::create('corp_moon_ledgers', function(Blueprint $table) {
                $table->unsignedBigIncrements('id');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->unsignedBigInteger('character_id');
                $table->string('character_name');
                $table->unsignedBigInteger('observer_id');
                $table->string('observer_name');
                $table->unsignedBigInteger('type_id');
                $table->string('ore');
                $table->unsignedBigInteger('quantity');
                $table->unsignedBigInteger('recorded_corporation_id');
                $table->string('record_corporation_name');
                $table->dateTime('last_updated');
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
        Schema::dropIfExists('alliance_rental_moon_ledgers');
        Schema::dropIfExists('corp_moon_ledgers');
    }
}
