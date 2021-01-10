<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiningTaxTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alliance_mining_tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('alliance_mining_tax_ledgers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('alliance_mining_tax_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mining_tax_tables');
    }
}
