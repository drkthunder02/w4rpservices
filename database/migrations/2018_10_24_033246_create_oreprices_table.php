<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrepricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('OrePrices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Name');
            $table->integer('ItemId');
            $table->decimal('BatchPrice', 20,2);
            $table->decimal('UnitPrice', 20, 2);
            $table->decimal('m3Price', 20, 2);
            $table->string('Time');
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
        Schema::dropIfExists('OrePrices');
    }
}
