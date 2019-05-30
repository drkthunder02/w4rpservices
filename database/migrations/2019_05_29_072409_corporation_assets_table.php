<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorporationAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_structures')) {
            Schema::create('alliance_structures', function(Blueprint $table) {
                $table->increments('id');
                $table->string('structure_id')->unique();
                $table->string('structure_name');
                $table->string('solar_system_id');
                $table->string('solar_system_name')->nullable();
                $table->string('type_id');
                $table->string('corporation_id');
                $table->boolean('services');
                $table->string('state');
                $table->dateTime('state_timer_start')->nullable();
                $table->dateTime('state_timer_end')->nullable();
                $table->dateTime('fuel_expires')->nullable();
                $table->string('profile_id');
                $table->string('position_x');
                $table->string('position_y');
                $table->string('position_z');
                $table->dateTime('next_reinforce_apply')->nullable();
                $table->integer('next_reinforce_hour')->nullable();
                $table->integer('next_reinforce_weekday')->nullable();
                $table->integer('reinforce_hour');
                $table->integer('reinforce_weekday')->nullable();
                $table->dateTime('unanchors_at')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('AllianceStructures')) {
            Schema::create('AllianceStructures', function(Blueprint $table) {
                $table->increments('id');
                $table->string('structure_id')->unique();
                $table->string('structure_name');
                $table->string('solar_system_id');
                $table->string('solar_system_name')->nullable();
                $table->string('type_id');
                $table->string('corporation_id');
                $table->boolean('services');
                $table->string('state');
                $table->dateTime('state_timer_start')->nullable();
                $table->dateTime('state_timer_end')->nullable();
                $table->dateTime('fuel_expires')->nullable();
                $table->string('profile_id');
                $table->string('position_x');
                $table->string('position_y');
                $table->string('position_z');
                $table->dateTime('next_reinforce_apply')->nullable();
                $table->integer('next_reinforce_hour')->nullable();
                $table->integer('next_reinforce_weekday')->nullable();
                $table->integer('reinforce_hour');
                $table->integer('reinforce_weekday')->nullable();
                $table->dateTime('unanchors_at')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_services')) {
            Schema::create('alliance_services', function(Blueprint $table) {
                $table->increments('id');
                $table->string('structure_id');
                $table->string('name');
                $table->string('state');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_assets')) {
            Schema::create('alliance_assets', function(Blueprint $table) {
                $table->increments('id');
                $table->boolean('is_blueprint_copy')->nullable();
                $table->boolean('is_singleton');
                $table->string('item_id');
                $table->string('location_flag');
                $table->string('location_id');
                $table->string('location_type');
                $table->integer('quantity');
                $table->string('type_id');
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
        Schema::dropIfExists('alliance_structures');
        Schema::dropIfExists('AllianceStructures');
        Schema::dropIfExists('alliance_services');
        Schema::dropIfExists('alliance_assets');
    }
}
