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
                $table->string('entity_id')->unique();
                $table->string('entity_name')->default('');
                $table->string('entity_type')->default('None');
                $table->text('reason');
                $table->text('alts')->nullable();
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
