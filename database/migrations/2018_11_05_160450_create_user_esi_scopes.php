<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEsiScopes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('UserEsiScopes')) {
            Schema::create('UserEsiScopes', function(Blueprint $table) {
                $table->integer('id')->increments();
                $table->integer('character_id');
                $table->foreign('character_id')->references('character_id')->on('users');
                $table->string('scope');
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
        Schema::dropIfExists('UserEsiScopes');
    }
}
