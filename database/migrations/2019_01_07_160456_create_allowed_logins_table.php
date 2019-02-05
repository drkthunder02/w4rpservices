<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllowedLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('allowed_logins')) {
            Schema::create('allowed_logins', function(Blueprint $table) {
                $table->increments('id');
                $table->string('entity_id');
                $table->string('entity_type');
                $table->string('entity_name');
                $table->string('login_type');
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
        Schema::dropIfExists('allowed_logins');
    }
}
