<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPlayerdonationjournalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('PlayerDonationJournal');
        Schema::dropIfExists('MarketOrders');
        Schema::dropIfExists('help_desk_ticket');
        Schema::dropIfExists('help_desk_tickets');
        Schema::dropIfExists('help_desk_ticket_responses');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
