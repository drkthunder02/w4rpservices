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
                $table->enum('corp_rented', [
                    'No',
                    'Yes',
                ])->default('No');
            });
        }

        if(!Schema::hasTable('alliance_mining_tax_wallet')) {
            Schema::create('alliance_mining_tax_wallet', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->unsignedBigInteger('character_id')->unique();
                $table->string('character_name');
                $table->decimal('amount', 20, 2)->default(0.00);                
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
        //Do nothing
    }
}
