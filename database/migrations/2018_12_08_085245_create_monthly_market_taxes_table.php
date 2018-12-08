<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyMarketTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('monthly_market_taxes')) {
            Schema::create('monthly_market_taxes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
                $table->decimal('tax_owed', 20, 2);
                $table->string('month');
                $table->string('year');
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
        Schema::dropIfExists('monthly_market_taxes');
    }
}
