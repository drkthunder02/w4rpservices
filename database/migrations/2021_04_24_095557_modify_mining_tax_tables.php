<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMiningTaxTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('alliance_mining_tax_observers')) {
            Schema::table('alliance_mining_tax_observers', function(Blueprint $table) {
                $table->string('observer_name')->nullable();
                $table->unsignedBigInteger('solar_system_id')->nullable();
                $table->string('solar_system_name')->nullable();
                $table->enum('corp_rented', [
                    'No',
                    'Yes',
                ])->default('No');
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
        //Drop the newly created columns
        Schema::table('alliance_mining_tax_observers', function(Blueprint $table) {
            $table->dropColumn(['observer_name']);
            $table->dropColumn(['solar_system_id']);
            $table->dropColumn(['solar_system_name']);
            $table->dropColumn(['corp_rented']);
        });
    }
}
