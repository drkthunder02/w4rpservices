<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJumpBridgeFuelLevelsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('alliance_flex_structures');
        Schema::dropIFExists('alliance_assets');
        Schema::dropIfExists('alliance_structures');
        Schema::dropIfExists('alliance_services');

        if(!Schema::hasTable('alliance_structures')) {
            Schema::create('alliance_structures', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('structure_id')->unique();
                $table->string('structure_name');
                $table->unsignedBigInteger('solar_system_id');
                $table->string('solar_system_name')->nullable();
                $table->unsignedBigInteger('type_id');
                $table->unsignedBigInteger('corporation_id');
                $table->boolean('services');
                $table->enum('state'. [

                ]);
                $table->dateTime('state_timer_start')->nullable();
                $table->dateTime('state_timer_end')->nullable();
                $table->dateTime('fuel_expires')->nullable();
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('position_x');
                $table->unsignedBigInteger('position_y');
                $table->unsignedBigInteger('position_z');
                $table->dateTime('next_reinforce_apply')->nullable();
                $table->unsignedInteger('next_reinforce_hour')->nullable();
                $table->unsignedInteger('next_reinforce_weekday')->nullable();
                $table->unsignedInteger('reinforce_hour');
                $table->unsignedInteger('reinforce_weekday')->nullable();
                $table->dateTime('unanchors_at')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_services')) {
            Schema::create('alliance_services', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('structure_id');
                $table->string('name');
                $table->enum('state', [

                ]);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_assets')) {
            Schema::create('alliance_assets', function(Blueprint $table) {
                $table->increments('id');
                $table->boolean('is_blueprint_copy')->nullable();
                $table->boolean('is_singleton');
                $table->unsignedBigInteger('item_id');
                $table->enum('location_flag', [

                ]);
                $table->unsignedBigInteger('location_id');
                $table->enum('location_type', [

                ]);
                $table->unsignedBigInteger('quantity');
                $table->unsignedBigInteger('type_id');
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
        Schema::dropIfExists('fleet_activity_tracking');
        Schema::dropIfExists('alliance_structures');
        Schema::dropIfExists('alliance_services');
        Schema::dropIfExists('alliance_assets');
    }
}
