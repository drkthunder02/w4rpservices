<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorpJournal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('CorpJournals')) {
            Schema::create('CorpJournals', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->integer('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('HoldingCorpFinancesJournal')) {
            Schema::create('HoldingCorpFinancesJournal', function(Blueprint $table) {
                $table->integer('id')->unique();
                $table->decimal('amount', 20, 2);
                $table->decimal('balance', 20, 2);
                $table->integer('context_id');
                $table->string('context_id_type');
                $table->dateTime('date');
                $table->string('description');
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->nullabe();
                $table->string('ref_type');
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
            });
        }

        if(!Schema::hasTable('jump_bridge_journal')) {
            Schema::create('jump_bridge_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->bigInteger('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('player_donation_journal')) {
            Schema::create('player_donation_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->bigInteger('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('reprocessing_tax_journal')) {
            Schema::create('reprocessing_tax_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->integer('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('office_fees_journal')) {
            Schema::create('office_fees_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->integer('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('structure_industry_tax_journal')) {
            Schema::create('structure_industry_tax_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->integer('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('planet_production_tax_journal')) {
            Schema::create('planet_production_tax_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corporation_id')->nullabe();
                $table->integer('division')->default(0);
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->integer('context_id')->nullable();
                $table->string('context_id_type')->nullable();
                $table->dateTime('date')->nullabe();
                $table->string('description')->nullabe();
                $table->integer('first_party_id')->nullable();
                $table->string('reason')->default(' ');
                $table->string('ref_type')->nullabe();
                $table->integer('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->integer('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('monthly_market_taxes')) {
            Schema::create('monthly_market_taxes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
                $table->decimal('tax_owed', 20, 2);
                $table->string('month');
                $table->string('year');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('pi_sale_journal')) {
            Schema::create('pi_sale_journal', function(Blueprint $table) {
                $table->string('id')->unique();
                $table->integer('corp_id');
                $table->integer('division');
                $table->integer('client_id');
                $table->string('date');
                $table->boolean('is_buy');
                $table->integer('journal_ref_id');
                $table->integer('location_id');
                $table->integer('quantity');
                $table->integer('transaction_id');
                $table->integer('type_id');
                $table->decimal('unit_price');
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
        Schema::dropIfExists('CorpWalletJournals');
        Schema::dropIfExists('HoldingcorpFinancesJournal');
        Schema::dropIfExists('jump_bridge_journal');
        Schema::dropIfExists('player_donation_journal');
        Schema::dropIfExists('reprocessing_tax_journal');
        Schema::dropIfExists('office_fees_journal');
        Schema::dropIfExists('structure_industry_tax');
        Schema::dropIfExists('planet_production_tax_journal');
        Schema::dropIfExists('monthly_market_taxes');
        Schema::dropIfExists('pi_sale_journal');
    }
}
