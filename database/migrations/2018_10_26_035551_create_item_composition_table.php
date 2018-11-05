<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemCompositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ItemComposition')) {
            Schema::create('ItemComposition', function (Blueprint $table) {
                $table->string('Name')->unique();
                $table->integer('ItemId');
                $table->decimal('m3Size', 10, 2)->default(0.00);
                $table->integer('BatchSize')->default(100);
                $table->integer('Tritanium')->default(0);
                $table->integer('Pyerite')->default(0);
                $table->integer('Mexallon')->default(0);
                $table->integer('Isogen')->default(0);
                $table->integer('Nocxium')->default(0);
                $table->integer('Zydrine')->default(0);
                $table->integer('Megacyte')->default(0);
                $table->integer('Morphite')->default(0);
                $table->integer('HeavyWater')->default(0);
                $table->integer('LiquidOzone')->default(0);
                $table->integer('NitrogenIsotopes')->default(0);
                $table->integer('HeliumIsotopes')->default(0);
                $table->integer('HydrogenIsotopes')->default(0);
                $table->integer('OxygenIsotopes')->default(0);
                $table->integer('StrontiumClathrates')->default(0);
                $table->integer('AtmosphericGases')->default(0);
                $table->integer('EvaporiteDeposits')->default(0);
                $table->integer('Hydrocarbons')->default(0);
                $table->integer('Silicates')->default(0);
                $table->integer('Cobalt')->default(0);
                $table->integer('Scandium')->default(0);
                $table->integer('Titanium')->default(0);
                $table->integer('Tungsten')->default(0);
                $table->integer('Cadmium')->default(0);
                $table->integer('Platinum')->default(0);
                $table->integer('Vanadium')->default(0);
                $table->integer('Chromium')->default(0);
                $table->integer('Technetium')->default(0);
                $table->integer('Hafnium')->default(0);
                $table->integer('Caesium')->default(0);
                $table->integer('Mercury')->default(0);
                $table->integer('Dysprosium')->default(0);
                $table->integer('Neodymium')->default(0);
                $table->integer('Promethium')->default(0);
                $table->integer('Thulium')->default(0);
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
        Schema::dropIfExists('ItemComposition');
    }
}
