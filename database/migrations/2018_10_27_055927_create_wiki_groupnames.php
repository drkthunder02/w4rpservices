<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikiGroupnames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::dropIfExists('wiki_groupname');
    }
}
