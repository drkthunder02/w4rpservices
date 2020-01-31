<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllianceMoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_moons')) {
            Schema::create('alliance_moons', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->unique();
                $table->string('Region');
                $table->string('System');
                $table->string('Planet');
                $table->string('Moon');
                $table->string('Corporation');
                $table->string('StructureName')->default('No Name');
                $table->string('FirstOre')->default('None');
                $table->integer('FirstQuantity')->default('0');
                $table->string('SecondOre')->default('None');
                $table->integer('SecondQuantity')->default('0');
                $table->string('ThirdOre')->default('None');
                $table->integer('ThirdQuantity')->default('0');
                $table->string('FourthOre')->default('None');
                $table->integer('FourthQuantity')->default('0');
                $table->string('Moon_Type');
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
        Schema::dropIfExists('alliance_moons');
    }
}
