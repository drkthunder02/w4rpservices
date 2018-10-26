<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Prices', function (Blueprint $table) {
            $table->increments('id');
            $table->text('Name');
            $table->integer('ItemId');
            $table->decimal('Price', 20, 2);
            $table->string('Time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Prices');
    }
}
