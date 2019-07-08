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
                $table->enum('availability', [
                    'public',
                    'personel',
                    'corporation',
                    'alliance',
                ]);
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
                $table->enum('status',[
                    'outstanding',
                    'in_progress',
                    'finished_issuer',
                    'finished_contractor',
                    'finished',
                    'cancelled',
                    'rejected',
                    'failed',
                    'deleted',
                    'reversed',
                ]);
                $table->string('title')->nullalbe();
                $table->enum('type', [
                    'unknown',
                    'item_exchange',
                    'auction',
                    'courier',
                    'loan',
                ]);
                $table->decimal('volume', 20, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('logistics_contracts')) {
            Schema::create('logistics_contracts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('contract_id');
                $table->string('status')->default('N/A');
                $table->enum('rush', [
                    'No',
                    'Yes',
                ])->default('No');
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
                $table->integer('isotopes');
            });
        }

        DB::table('solar_system_distances')->insert([
            'start_id' => '30001195',
            'start_name' => 'J-ODE7',
            'end_id' => '30002269',
            'end_name' => 'Ebo',
            'distance' => 16.953,
            'isotopes' => 53706,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => '30002269',
            'start_name' => 'Ebo',
            'end_id' => '30001195',
            'end_name' => 'J-ODE7',
            'distance' => 16.953,
            'isotopes' => 53706,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => '30001195',
            'start_name' => 'J-ODE7',
            'end_id' => '30002110',
            'end_name' => 'B9E-H6',
            'distance' => 5.587,
            'isotopes' => 17699,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => '30002110',
            'start_name' => 'B9E-H6',
            'end_id' => '30001195',
            'end_name' => 'J-ODE7',
            'distance' => 5.587,
            'isotopes' => 17699,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => 30002269,
            'start_name' => 'Ebo',
            'end_id' => 30002142,
            'end_name' => 'L-5JCJ',
            'distance' => 26.009,
            'isotopes' => 82393,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => 30002142,
            'start_name' => 'L-5JCJ',
            'end_id' => 30002269,
            'end_name' => 'Ebo',
            'distance' => 26.009,
            'isotopes' => 82393,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => 30001195,
            'start_name' => 'J-ODE7',
            'end_id' => 30002142,
            'end_name' => 'L-5JCJ',
            'distance'=> 8.779,
            'isotopes' => 27811,
        ]);

        DB::table('solar_system_distances')->insert([
            'start_id' => 30002142,
            'start_name' => 'L-5JCJ',
            'end_id' => 30001195,
            'end_name' => 'J-ODE7',
            'distance' => 8.779,
            'isotopes' => 278111,
        ]);

        if(!Schema::hasTable('logistics_routes')) {
            Schema::create('logistics_routes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->decimal('price_per_m3', 20, 2);
                $table->decimal('max_size', 20, 2);
            });
        }

        DB::table('logistics_routes')->insert([
            'name' => 'J-ODE7 -> Ebo',
            'price_per_m3' => 300.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Ebo -> J-ODE7',
            'price_per_m3' => 300.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'J-ODE7 -> B9E-H6',
            'price_per_m3' => 150.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'B9E-H6 -> J-ODE7',
            'price_per_m3' => 150.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Ebo -> L-5JCJ',
            'price_per_m3' => 600.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'L-5JCJ -> Ebo',
            'price_per_m3' => 600.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'J-ODE7 -> L-5JCJ',
            'price_per_m3' => 300.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'L-5JCJ -> J-ODE7',
            'price_per_m3' => 300.00,
            'max_size' => 300000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Jita -> U-QVWD',
            'price_per_m3'=> 950.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'U-QVWD -> Jita',
            'price_per_m3' => 950.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Jita -> J-ODE7',
            'price_per_m3' => 1000.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'J-ODE7-> Jita',
            'price_per_m3' => 1000.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Jita -> B9E-H6',
            'price_per_m3' => 1000.00,
            'max_size'=> 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'B9E-H6 -> Jita',
            'price_per_m3' => 1000.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'Jita -> UALX-3',
            'price_per_m3' => 1000.00,
            'max_size' => 330000.00,
        ]);

        DB::table('logistics_routes')->insert([
            'name' => 'UALX-3 -> Jita',
            'price_per_m3' => 1000.00,
            'max_size' => 330000.00,
        ]);

        if(!Schema::hasTable('logistics_insurance_deposits')) {
            Schema::create('logistics_insurance_deposits', function(Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
                $table->decimal('amount', 20, 2);
            });
        }

        if(!Schema::hasTable('logistics_insurance_payouts')) {
            Schema::create('logistics_insurance_payouts', function(Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name')->nullalbe();
                $table->string('corporation_id');
                $table->string('corporation_name')->nullable();
                $table->string('authorized_by_id');
                $table->string('authorized_by_name')->nullalbe();
                $table->decimal('amount', 20, 2)->nullalbe();
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
        Schema::dropIfExists('eve_contracts');
        Schema::dropIfExists('logistics_contracts');
        Schema::dropIfExists('solar_systems');
        Schema::dropIfExists('solar_system_distances');
        Schema::dropIfExists('logistics_routes');
        Schema::dropIfExists('logistics_insurance_deposits');
        Schema::dropIfExists('logistics_insurance_payouts');
    }
}
