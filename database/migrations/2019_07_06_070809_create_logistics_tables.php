<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogisticsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('eve_contracts')) {
            Schema::create('eve_contracts', function (Blueprint $table) {
                $table->string('contract_id')->unique();
                $table->string('acceptor_id');
                $table->string('assignee_id');
                $table->string('availability');
                $table->string('buyout')->nullable();
                $table->string('collateral')->nullable();
                $table->dateTime('date_accepted')->nullable();
                $table->dateTime('date_completed')->nullable();
                $table->dateTime('date_expired');
                $table->dateTime('date_issued');
                $table->integer('days_to_complete')->nullable();
                $table->string('end_location_id')->nullable();
                $table->boolean('for_corporation');
                $table->string('issuer_corporation_id');
                $table->string('issuer_id');
                $table->decimal('price', 20, 2)->default(0.00);
                $table->decimal('reward', 20, 2)->default(0.00);
                $table->string('start_location_id')->nullable();
                $table->string('status');
                $table->string('title')->nullalbe();
                $table->decimal('volume', 20, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('logistics_contracts')) {
            Schema::create('logistics_contracts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('contract_id');
                $table->string('accepted')->default('No');
                $table->string('accepted_by_id')->nullalbe();
                $table->string('accepted_by_name')->nullalbe();
                $table->string('status')->default('N/A');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('solar_systems')) {
            Schema::create('solar_systems', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('solar_system_id')->unique();
            });
        }

        if(!Schema::hasTable('solar_system_distances')) {
            Schema::create('solar_system_distances', function (Blueprint $table) {
                $table->increments('id');
                $table->string('start_id');
                $table->string('start_name');
                $table->string('end_id');
                $table->string('end_name');
                $table->decimal('distance', 20, 6);
            });
        }

        DB::table('solar_system_distances')->insert([
            'start_id'
            'start_name'
            'end_id'
            'end_name'
            'distance'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eve_contracts');
        Schema::dropIfExists('logistics_contracts');
        Schema::dropIfExists('solar_systems');
        Schema::dropIfExists('solar_system_distances');
    }
}
