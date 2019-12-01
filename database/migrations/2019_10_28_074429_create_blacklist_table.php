<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_blacklist')) {
            Schema::create('alliance_blacklist', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('character_id')->unique();
                $table->string('name')->unique();
                $table->text('reason');
                $table->text('alts');
                $table->string('lister_id');
                $table->string('lister_name');
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
        Schema::dropIfExists('alliance_blacklist');
    }
}
