<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsiScopes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('EsiScopes')) {
            Schema::create('EsiScopes', function(Blueprint $table) {
                $table->integer('id')->increments();
                $table->integer('character_id');
                $table->string('scope');
                $table->timestamps();
            });
        }

        Schema::dropIfExists('UserEsiScopes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EsiScopes');
    }
}
