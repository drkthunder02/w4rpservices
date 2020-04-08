<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFleetTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_fleets')) {
            Schema::create('alliance_fleets', function(Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigUnsignedInteger('fleet_id');
                $table->bigUnsignedInteger('fleet_commander_id');
                $table->string('fleet_commander_name')->nullable();
                $table->unsignedInteger('member_count');
                $table->dateTime('fleet_opened_time');
                $table->dateTime('fleet_closed_time')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_fleet_members')) {
            Schema::create('alliance_fleet_members', function(Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigUnsignedInteger('fleet_id');
                $table->bigUnsignedInteger('character_id');
                $table->string('character_name');
                $table->dateTime('fleet_joined_time');
                $table->dateTime('fleet_leaved_time');
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
        Schema::dropIfExists('alliance_fleets');
        Schema::dropIfExists('alliance_fleet_members');
    }
}
