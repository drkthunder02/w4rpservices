<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('clones')) {
            Schema::create('clones', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->boolean('active');
                $table->timestamps();
            });
        }
        
        if(!Schema::hasTable('clones_mailing')) {
            Schema::create('clones_mailing', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->integer('time_sent');
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
        Schema::dropIfExists('clones');
        Schema::dropIfExists('clones_mailing');
    }
}
