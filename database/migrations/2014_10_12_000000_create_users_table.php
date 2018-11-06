<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('character_id')->unsigned()->unique();
                $table->string('avatar');
                $table->string('access_token')->nullable();
                $table->string('refresh_token')->nullable();
                $table->integer('inserted_at')->default(0);
                $table->integer('expires_in')->default(0);
                $table->string('owner_hash');
                $table->string('user_type')->default('Guest');
                $table->string('role')->default('Guest');
                $table->text('scopes')->nullable();
                $table->string('email')->unique()->nullable();
                $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
