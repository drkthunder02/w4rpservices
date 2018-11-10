<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsiTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('EsiTokens')) {
            Schema::create('EsiTokens', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('character_id')->unique();
                $table->string('access_token');
                $table->string('refresh_token');
                $table->integer('expires_in');
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
        Schema::dropIfExists('EsiTokens');
    }
}
