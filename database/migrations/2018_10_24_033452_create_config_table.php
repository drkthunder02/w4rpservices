<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('Config')) {
            Schema::create('Config', function (Blueprint $table) {
                $table->decimal('RentalTax', 5,2);
                $table->decimal('AllyRentalTax', 5, 2);
                $table->decimal('RefineRate', 5, 2);
                $table->integer('RentalTime');
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
        Schema::dropIfExists('Config');
    }
}
