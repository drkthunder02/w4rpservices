<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_alts')) {
            Schema::create('user_alts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('main_id')->unsigned();
                $table->integer('character_id')->unsigned()->unique();
                $table->string('avatar');
                $table->string('access_token')->nullable();
                $table->string('refresh_token')->nullable();
                $table->integer('inserted_at')->default(0);
                $table->integer('expires_in')->default(0);
                $table->string('owner_hash');
                $table->rememberToken();
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
        Schema::dropIfExists('user_alts');
    }
}
