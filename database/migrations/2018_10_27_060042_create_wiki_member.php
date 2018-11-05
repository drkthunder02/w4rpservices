<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikiMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('wiki_member')) {
            Schema::create('wiki_member', function(Blueprint $table) {
                $table->integer('uid');
                $table->integer('gid');
                $table->string('groupname');
                $table->primary(['uid', 'gid']);
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
        Schema::dropIfExists('wiki_member');
    }
}
