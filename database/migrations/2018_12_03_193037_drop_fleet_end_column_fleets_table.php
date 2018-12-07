<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFleetEndColumnFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('Fleets', 'fleet_end')) {
            Schema::table('Fleets', function($table) {
                $table->dropColumn('fleet_end');
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
        Schema::table('Fleets', function($table) {
            $table->dateTime('fleet_end');
        });
    }
}
