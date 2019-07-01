<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrpTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('srp_ships')) {
            Schema::create('srp_ships', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id')->default('N/A');
                $table->string('character_name');
                $table->string('fleet_commander_id')->default('0');
                $table->string('fleet_commander_name');
                $table->string('zkillboard');
                $table->string('ship_type');
                $table->string('fleet_type');
                $table->decimal('loss_value', 20, 2);
                $table->string('approved')->default('Under Review');
                $table->decimal('paid_value', 20, 2)->default(0.00);
                $table->string('notes')->nullable();
                $table->string('paid_by_id')->nullable();
                $table->string('paid_by_name')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('srp_fleet_types')) {
            Schema::create('srp_fleet_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code');
                $table->string('description');
            });
        }

        if(!Schema::hasTable('srp_ship_types')) {
            Schema::create('srp_ship_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code');
                $table->string('description');
            });
        }

        if(!Schema::hasTable('srp_payouts')) {
            Schema::create('srp_payouts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code');
                $table->decimal('payout', 5, 2);
            });
        }

        DB::table('srp_fleet_types')->insert([
            'code' => 'None',
            'description' => 'None',
        ]);

        DB::table('srp_fleet_types')->insert([
            'code' => 'Home Defense',
            'description' => 'Home Defense',
        ]);

        DB::table('srp_fleet_types')->insert([
            'code' => 'Legacy Ops',
            'description' => 'Legacy Ops',
        ]);

        DB::table('srp_fleet_types')->insert([
            'code' => 'Strat Op',
            'description' => 'Strat Op',
        ]);

        DB::table('srp_fleet_types')->insert([
            'code' => 'CTA',
            'description' => 'CTA',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'None',
            'description' => 'None',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T1FDC',
            'description' => 'T1 Frig / Dessie / Cruiser',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T1BC',
            'description' => 'T1 Battelcruiser',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T2F',
            'description' => 'T2 Frigate',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T3D',
            'description' => 'T3 Destroyer',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T1T2Logi',
            'description' => 'T1 & T2 Logistics',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'RI',
            'description' => 'Recons / Interdictors',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T2C',
            'description' => 'T2 Cruiser',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'T3C',
            'description' => 'T3 Cruiser',
        ]);

        DB::table('srp_ship_types')->insert([
            'code' => 'COM',
            'description' => 'Command Ship',
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T1FDC',
            'payout' => 75.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T1BC',
            'payout' => 60.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T2F',
            'payout' => 60.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T3D',
            'payout' => 60.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T1T2Logi',
            'payout' => 100.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'RI',
            'payout' => 50.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T2C',
            'payout' => 50.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'T3C',
            'payout' => 50.00,
        ]);

        DB::table('srp_payouts')->insert([
            'code' => 'COM',
            'payout' => 100.00,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srp_ships');
        Schema::dropIfExists('srp_ship_types');
        Schema::dropIfExists('srp_fleet_types');
        Schema::dropIfExists('srp_payouts');
    }
}
