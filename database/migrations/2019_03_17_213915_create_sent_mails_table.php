<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_mails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sender');
            $table->string('subject');
            $table->text('body');
            $table->string('recipient');
            $table->string('recipient_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_mails');
    }
}
