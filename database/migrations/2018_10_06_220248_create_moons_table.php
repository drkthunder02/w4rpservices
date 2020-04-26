<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('Moons')) {
            Schema::create('Moons', function (Blueprint $table) {
                $table->increments('id');
                $table->string('Region');
                $table->string('System');
                $table->string('Planet');
                $table->string('Moon');
                $table->unsignedBigInteger('StructureId')->nullable();
                $table->string('StructureName')->default('No Name');
                $table->string('FirstOre')->default('None');
                $table->integer('FirstQuantity')->default('0');
                $table->string('SecondOre')->default('None');
                $table->integer('SecondQuantity')->default('0');
                $table->string('ThirdOre')->default('None');
                $table->integer('ThirdQuantity')->default('0');
                $table->string('FourthOre')->default('None');
                $table->integer('FourthQuantity')->default('0');
                $table->string('RentalCorp')->default('Not Rented');
                $table->integer('RentalEnd')->default('0');
                $table->string('Paid')->default('No');
            });
        }

        if(!Schema::hasTable('moon_rents')) {
            Schema::create('moon_rents', function (Blueprint $table) {
                $table->increments('id');
                $table->string('System');
                $table->string('Planet');
                $table->string('Moon');
                $table->unsignedBigInteger('StructureId')->nullable();
                $table->string('RentalCorp');
                $table->dateTime('RentalEnd');
                $table->string('Contact');
                $table->string('Price');
                $table->string('Type');
                $table->string('Paid');
                $table->string('Paid_Until')->nullable();
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
        Schema::dropIfExists('Moons');
        Schema::dropIfExists('moon_rents');
    }
}
