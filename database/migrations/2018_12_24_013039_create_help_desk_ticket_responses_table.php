<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpDeskTicketResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('help_desk_ticket_responses')) {
            Schema::create('help_desk_ticket_responses', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('ticket_id');
                $table->string('assigned_id');
                $table->text('body');
                $table->timestamps();

            });
        }
        Schema::create('help_desk_ticket_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_desk_ticket_responses');
    }
}
