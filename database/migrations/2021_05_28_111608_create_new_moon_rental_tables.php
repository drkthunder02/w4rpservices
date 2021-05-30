<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewMoonRentalTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('moon_lookup')) {
            Schema::create('moon_lookup', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moon_id');
                $table->string('name');
                $table->double('position_x');
                $table->double('position_y');
                $table->double('position_z');
                $table->unsignedBigInteger('system_id');
            });
        }

        if(!Schema::hasTable('alliance_moons')) {
            Schema::create('alliance_moons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moon_id');
                $table->string('name')->default('Not Assigned');
                $table->unsignedBigInteger('system_id');
                $table->string('system_name')->default('Not Assigned');
                $table->decimal('worth_amount')->default(0.00);
                $table->enum('rented', [
                    'No',
                    'Yes',
                ])->default('No');
                $table->decimal('rental_amount')->default(0.00);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_moon_ores')) {
            Schema::create('alliance_moon_ores', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moon_id');
                $table->string('moon_name');
                $table->unsignedBigInteger('ore_type_id');
                $table->string('ore_name');
                $table->float('quantity');
                $table->unsignedBigInteger('solar_system_id');
                $table->unsignedBigInteger('planet_id');
            });
        }

        if(!Schema::hasTable('alliance_moon_rentals')) {
            Schema::create('alliance_moon_rentals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moon_id');
                $table->string('moon_name')->default('Not Assigned');
                $table->decimal('rental_amount', 20, 2)->default(0.00);
                $table->date('rental_start')->nullable();
                $table->date('rental_end')->nullable();
                $table->date('next_billing_date')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('entity_name')->nullable();
                $table->enum('entity_type', [
                    'None',
                    'Character',
                    'Corporation',
                    'Alliance',
                ])->default('None');
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
        Schema::dropIfExists('moon_lookup');
        Schema::dropIfExists('alliance_moons');
        Schema::dropIfExists('alliance_moon_ores');
        Schema::dropIfExists('alliance_moon_rentals');
    }
}
