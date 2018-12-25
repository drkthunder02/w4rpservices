<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpDeskTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('help_desk_tickets')) {
            Schema::create('help_desk_tickets', function(Blueprint $table) {
                $table->increments('ticket_id');
                $table->string('user_id');
                $table->string('assigned_id');
                $table->string('department');
                $table->string('subject');
                $table->text('body');
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
        Schema::dropIfExists('help_desk_tickets');
    }
}
