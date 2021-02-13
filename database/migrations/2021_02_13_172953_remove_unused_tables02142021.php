<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedTables02142021 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('wiki_groupnames');
        Schema::dropIfExists('wiki_member');
        Schema::dropIfExists('wiki_user');
        Schema::dropIfExists('PlayerDonationJournals');
        Schema::dropIfExists('corp_mining_observers');
        Schema::dropIfExists('corp_moon_ledgers');
        Schema::dropIfExists('alliance_anchor_structure');
        Schema::dropIfExists('alliance_rental_moons');
        Schema::dropIfExists('alliance_moon_rental_invoices');
        Schema::dropIfExists('alliance_moon_rental_payments');
        Schema::dropIfExists('alliance_moon_requests');
        Schema::dropIfExists('alliance_rental_moons');
        Schema::dropIfExists('alliance_rental_moon_ledgers');
        Schema::dropIfExists('alliance_rental_systems');
        Schema::dropIfExists('fleet_activity_tracking');
        Schema::dropIfExists('Fleets');
        Schema::dropIfExists('eve_mails');
        Schema::dropIfExists('bucks_character_wallet_entries');
        Schema::dropIfExists('bucks_alliance_systems');
        Schema::dropIfExists('bucks_ratting_daily_pool');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Do nothing since we are only removing tables
    }
}
