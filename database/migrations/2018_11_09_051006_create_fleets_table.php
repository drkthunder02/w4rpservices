<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('Fleets')) {
            Schema::create('Fleets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('fleet')->unique();
                $table->text('description')->nullable();
                $table->dateTime('creation_time');
                $table->dateTime('fleet_end');
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
        Schema::dropIfExists('Fleets');
    }
}
