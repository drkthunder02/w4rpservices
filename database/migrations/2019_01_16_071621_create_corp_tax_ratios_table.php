<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorpTaxRatiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('corp_tax_ratios')) {
            Schema::create('corp_tax_ratios', function (Blueprint $table) {
                $table->increments('id');
                $table->string('corporation_id');
                $table->string('structure_type');
                $table->string('ratio');
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
        Schema::dropIfExists('corp_tax_ratios');
    }
}
