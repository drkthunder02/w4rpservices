<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('eve_mails')) {
            Schema::create('eve_mails', function (Blueprint $table) {
                $table->increments('id');
                $table->string('sender');
                $table->string('recipient');
                $table->string('subject');
                $table->string('body');
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
        Schema::dropIfExists('eve_mails');
    }
}
