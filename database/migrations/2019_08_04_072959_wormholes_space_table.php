<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WormholesSpaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_wormholes')) {
            Schema::create('alliance_wormholes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('system');
                $table->string('sig_id');
                $table->string('duration_left');
                $table->dateTime('dateTime');
                $table->enum('class', [
                    'C1',
                    'C2',
                    'C3',
                    'C4',
                    'C5',
                    'C6',
                    'C7',
                    'C8',
                    'C9',
                    'C13',
                    'Drifter',
                    'Thera',
                    'Exit WH',
                ]);
                $table->string('type');
                $table->enum('hole_size', [
                    'XS',
                    'S',
                    'M',
                    'L',
                    'XL',
                ]);
                $table->enum('stability', [
                    'Stable',
                    'Non-Critical',
                    'Critical',
                ]);
                $table->text('details')->nullable();
                $table->string('link')->nullable();
                $table->unsignedInteger('mass_allowed');
                $table->unsignedInteger('individual_mass');
                $table->unsignedInteger('regeneration');
                $table->enum('stable_time', [
                    '>24 hours',
                    '>4 hours <24 hours',
                    '<4 hours',
                ]);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('wormhole_types')) {
            Schema::create('wormhole_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type');
                $table->string('leads_to');
                $table->unsignedInteger('mass_allowed');
                $table->unsignedInteger('individual_mass');
                $table->unsignedInteger('regeneration');
                $table->unsignedInteger('max_stable_time');
            });
        }

        DB::table('wormhole_types')->insert([
            'type' => 'A009',
            'type' => 'C13',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'A239',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'A641',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'A982',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'B041',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 5000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 48,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'B274',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'B449',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'B520',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 5000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'B735',
            'type' => 'Drifter',
            'leads_to' => 'Barbican',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C008',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C125',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C140',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C247',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C248',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 5000000000,
            'individual_mass' => 1800000000,
            'regeneration' => 500000000,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C391',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 5000000000,
            'individual_mass' => 1800000000,
            'regeneration' => 500000000,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'C414',
            'type' => 'Drifter',
            'leads_to' => 'Conflux',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'D364',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'D382',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'D792',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'D845',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'E004',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'E175',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'E545',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'E587',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'F135',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'F353',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 100000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'F355',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 100000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'G008',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'G024',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'H121',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'H296',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'H900',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'I182',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'J244',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'K162',
            'type' => 'Exit WH',
            'leads_to' => 'Exit',
            'mass_allowed' => 0,
            'individual_mass' => 0,
            'regeneration' => 0,
            'max_stable_time' => 0,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'K329',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 5000000000,
            'individual_mass' => 1800000000,
            'regeneration' => 500000000,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'K346',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'L005',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'L031',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'L477',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'L614',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'M001',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'M164',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'M267',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'M555',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'M609',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N062',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N110',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N290',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 5000000000,
            'individual_mass' => 1800000000,
            'regeneration' => 500000000,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N432',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N766',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N770',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N944',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'N968',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'O128',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 300000000,
            'regeneration' => 100000000,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'O477',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'O883',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'P060',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Q003',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 500000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Q063',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Q317',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'R051',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'R259',
            'type' => 'Drifter',
            'leads_to' => 'Redoubt',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'R474',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'R943',
            'type' => 'C2',
            'leads_to' => 'W-Space',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'S047',
            'type' => 'C7',
            'leads_to' => 'Highsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'S199',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'S804',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'S877',
            'type' => 'Drifter',
            'leads_to' => 'Sentinel',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'T405',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'T458',
            'type' => 'Thera',
            'leads_to' => 'Thera',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'U210',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'U319',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1800000000,
            'regeneration' => 500000000,
            'max_stable_time' => 48,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'U574',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V283',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1000000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V301',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V753',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V898',
            'type' => 'C8',
            'leads_to' => 'Lowsec',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V911',
            'type' => 'C5',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'V928',
            'type' => 'Drifter',
            'leads_to' => 'Vidette',
            'mass_allowed' => 750000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'W237',
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'X702',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'X877',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Y683',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Y790',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z006',
            'type' => 'C3',
            'leads_to' => 'W-Space',
            'mass_allowed' => 1000000000,
            'individual_mass' => 5000000,
            'regeneration' => 5000000,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z060',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 1000000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z142',
            'type' => 'C9',
            'leads_to' => 'Nullsec',
            'mass_allowed' => 3000000000,
            'individual_mass' => 1350000000,
            'regeneration' => 0,
            'max_stable_time' => 24,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z457',
            'type' => 'C4',
            'leads_to' => 'W-Space',
            'mass_allowed' => 2000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z647',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 500000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);

        DB::table('wormhole_types')->insert([
            'type' => 'Z971',
            'type' => 'C1',
            'leads_to' => 'W-Space',
            'mass_allowed' => 100000000,
            'individual_mass' => 20000000,
            'regeneration' => 0,
            'max_stable_time' => 16,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alliance_wormholes');
        Schema::dropIfExists('wormhole_types');
    }
}
