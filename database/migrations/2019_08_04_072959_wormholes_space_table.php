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
                $table->string('sig_id');
                $table->string('duration_left');
                $table->string('date_scanned');
                $table->string('time_scanned');
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
                $table->text('details');
                $table->string('link');
                $table->decimal('mass_allowed', 20, 2);
                $table->decimal('individual_mass', 20, 2);
                $table->decimal('regeneration', 20, 2);
                $table->decimal('max_stable_time', 7, 0);
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
            'type' => 'C6',
            'leads_to' => 'W-Space',
            'mass_allowed' => 5000000000,
            'individual_mass' => 300000000,
            'regeneration' => 0,
            'max_stable_time' => 48,
        ]);
    }

 
    B735 	Drifter - Barbican 	750,000,000 	300,000,000 	0 		16
    C008 	Class 5 W-space 	1,000,000,000 	5,000,000 	500,000,000 	5 	16
    C125 	Class 2 W-Space 	1,000,000,000 	20,000,000 	0 	2 	16
    C140 	Lowsec 	3,000,000,000 	1,350,000,000 	0 	8 	24
    C247 	Class 3 W-space 	2,000,000,000 	300,000,000 	0 	3 	16
    C248 	Nullsec (0.0) 	5,000,000,000 	1,800,000,000 	500,000,000 	9 	24
    C391 	Lowsec 	5,000,000,000 	1,800,000,000 	500,000,000 	8 	24
    C414 	Drifter - Conflux 	750,000,000 	300,000,000 	0 		16
    D364 	Class 2 W-space 	1,000,000,000 	300,000,000 	0 	2 	16
    D382 	Class 2 W-space 	2,000,000,000 	300,000,000 	0 	2 	16
    D792 	Highsec 	3,000,000,000 	1,000,000,000 	0 	7 	24
    D845 	Highsec 	5,000,000,000 	300,000,000 	500,000,000 	7 	24
    E004 	Class 1 W-Space 	1,000,000,000 	5,000,000 	500,000,000 	1 	16
    E175 	Class 4 W-Space 	2,000,000,000 	300,000,000 	0 	4 	16
    E545 	Nullsec (0.0) 	2,000,000,000 	300,000,000 	0 	9 	24
    E587 	Nullsec (0.0) 	3,000,000,000 	1,000,000,000 	0 	9 	16
    F135 	Thera 	750,000,000 	300,000,000 	0 		16
    F353 	Thera 	100,000,000 	20,000,000 	0 		16
    F355 	Thera 	100,000,000 	20,000,000 	0 		16
    G008 	Class 6 W-Space 	1,000,000,000 	5,000,000 	500,000,000 	6 	16
    G024 	Class 2 W-Space 	2,000,000,000 	300,000,000 	0 	2 	16
    H121 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    H296 	Class 5 W-Space 	3,000,000,000 	1,350,000,000 	0 	5 	24
    H900 	Class 5 W-Space 	3,000,000,000 	300,000,000 	0 	5 	24
    I182 	Class 2 W-Space 	2,000,000,000 	300,000,000 	0 	2 	16
    J244 	Lowsec 	1,000,000,000 	20,000,000 	0 	8 	24
    K162 	Exit WH 	? 	? 	? 	? 	?
    K329 	Nullsec (0.0) 	5,000,000,000 	1,800,000,000 	500,000,000 	9 	24
    K346 	Nullsec (0.0) 	3,000,000,000 	300,000,000 	0 	9 	24
    L005 	Class 2 W-Space 	1,000,000,000 	5,000,000 	500,000,000 	2 	16
    L031 	Thera 	3,000,000,000 	1,000,000,000 	0 		16
    L477 	Class 3 W-Space 	2,000,000,000 	300,000,000 	0 	3 	16
    L614 	Class 5 W-Space 	1,000,000,000 	20,000,000 	0 	5 	24
    M001 	Class 4 W-Space 	1,000,000,000 	5,000,000 	500,000,000 	4 	16
    M164 	Thera 	2,000,000,000 	300,000,000 	0 		16
    M267 	Class 3 W-Space 	1,000,000,000 	300,000,000 	0 	3 	16
    M555 	Class 5 W-Space 	3,000,000,000 	1,000,000,000 	0 	5 	24
    M609 	Class 4 W-Space 	1,000,000,000 	20,000,000 	0 	4 	16
    N062 	Class 5 W-Space 	3,000,000,000 	300,000,000 	0 	5 	24
    N110 	Highsec 	1,000,000,000 	20,000,000 	0 	7 	24
    N290 	Lowsec 	5,000,000,000 	1,800,000,000 	500,000,000 	8 	24
    N432 	Class 5 W-Space 	3,000,000,000 	1,350,000,000 	0 	5 	24
    N766 	Class 2 W-Space 	2,000,000,000 	300,000,000 	0 	2 	16
    N770 	Class 5 W-Space 	3,000,000,000 	300,000,000 	0 	5 	24
    N944 	Lowsec 	3,000,000,000 	1,350,000,000 	0 	8 	24
    N968 	Class 3 W-Space 	2,000,000,000 	300,000,000 	0 	3 	16
    O128 	Class 4 W-Space 	1,000,000,000 	300,000,000 	100,000,000 	4 	24
    O477 	Class 3 W-Space 	2,000,000,000 	300,000,000 	0 	3 	16
    O883 	Class 3 W-Space 	1,000,000,000 	20,000,000 	0 	3 	16
    P060 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    Q003 	Nullsec (0.0) 	1,000,000,000 	5,000,000 	500,000,000 	9 	16
    Q063 	Highsec 	500,000,000 	20,000,000 	0 	7 	16
    Q317 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    R051 	Lowsec 	3,000,000,000 	1,000,000,000 	0 	8 	16
    R259 	Drifter - Redoubt 	750,000,000 	300,000,000 	0 		16
    R474 	Class 6 W-Space 	3,000,000,000 	300,000,000 	0 	6 	24
    R943 	Class 2 W-Space 	750,000,000 	300,000,000 	0 	2 	16
    S047 	Highsec 	3,000,000,000 	300,000,000 	0 	7 	24
    S199 	Nullsec (0.0) 	3,000,000,000 	1,350,000,000 	0 	9 	24
    S804 	Class 6 W-Space 	1,000,000,000 	20,000,000 	0 	6 	24
    S877 	Drifter - Sentinel 	750,000,000 	300,000,000 	0 		16
    T405 	Class 4 W-Space 	2,000,000,000 	300,000,000 	0 	4 	16
    T458 	Thera 	500,000,000 	20,000,000 	0 		16
    U210 	Lowsec 	3,000,000,000 	300,000,000 	0 	8 	24
    U319 	Class 6 W-Space 	3,000,000,000 	1,800,000,000 	500,000,000 	6 	48
    U574 	Class 6 W-Space 	3,000,000,000 	300,000,000 	0 	6 	24
    V283 	Nullsec (0.0) 	3,000,000,000 	1,000,000,000 	0 	9 	24
    V301 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    V753 	Class 6 W-Space 	3,000,000,000 	1,350,000,000 	0 	6 	24
    V898 	Lowsec 	2,000,000,000 	300,000,000 	0 		16
    V911 	Class 5 W-Space 	3,000,000,000 	1,350,000,000 	0 	5 	24
    V928 	Drifter - Vidette 	750,000,000 	300,000,000 	0 		16
    W237 	Class 6 W-Space 	3,000,000,000 	1,350,000,000 	0 	6 	24
    X702 	Class 3 W-Space 	1,000,000,000 	300,000,000 	0 	3 	24
    X877 	Class 4 W-Space 	2,000,000,000 	300,000,000 	0 	4 	16
    Y683 	Class 4 W-Space 	2,000,000,000 	300,000,000 	0 	4 	16
    Y790 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    Z006 	Class 3 W-Space 	1,000,000,000 	5,000,000 	5,000,000 	1 	16
    Z060 	Nullsec (0.0) 	1,000,000,000 	20,000,000 	0 	9 	24
    Z142 	Nullsec (0.0) 	3,000,000,000 	1,350,000,000 	0 	9 	24
    Z457 	Class 4 W-Space 	2,000,000,000 	300,000,000 	0 	4 	16
    Z647 	Class 1 W-Space 	500,000,000 	20,000,000 	0 	1 	16
    Z971 	Class 1 W-Space 	100,000,000 	20,000,000 	0 	1 	16 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
