<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoonRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moon_rents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('System');
            $table->string('Planet');
            $table->string('Moon');
            $table->string('RentalCorp');
            $table->dateTime('RentalEnd');
            $table->string('Contact');
            $table->string('Price');
            $table->string('Type');
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
        Schema::dropIfExists('moon_rents');
    }
}
