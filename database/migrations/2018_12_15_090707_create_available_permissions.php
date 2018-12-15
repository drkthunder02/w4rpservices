<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailablePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('available_user_permissions')) {
            Schema::create('available_user_permissions', function(Blueprint $table) {
                $table->increments('id');
                $table->string('permission');
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
        Schema::dropIfExists('available_user_permissions');
    }
}
