<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikiUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('wiki_user')) {
            Schema::create('wiki_user', function(Blueprint $table) {
                $table->increments('id');
                $table->string('login');
                $table->string('pass');
                $table->string('name');
                $table->string('mail')->default('')->nullable();
                $table->unique('login', 'user');
            });
        }

        if(!Schema::hasTable('wiki_member')) {
            Schema::create('wiki_member', function(Blueprint $table) {
                $table->integer('uid');
                $table->integer('gid');
                $table->string('groupname');
                $table->primary(['uid', 'gid']);
            });
        }

        if(!Schema::hasTable('wiki_groupnames')) {
            Schema::create('wiki_groupnames', function(Blueprint $table) {
                $table->increments('id');
                $table->string('gname');
                $table->unique('id', 'id');
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
        Schema::dropIfExists('wiki_user');
        Schema::dropIfExists('wiki_member');
        Schema::dropIfExists('wiki_groupname');
    }
}
