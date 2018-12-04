<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserToCorporationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_to_corporation')) {
            Schema::create('user_to_corporation', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
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
        Schema::dropIfExists('user_to_corporation');
    }
}
