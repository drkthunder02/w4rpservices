<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructureMarketJournalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('corp_market_journal')) {
            Schema::create('corp_market_journal', function(Blueprint $table) {
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

        if(!Schema::hasTable('corp_market_structures')) {
            Schema::create('corp_market_structures', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('character_id');
                $table->unsignedInteger('corporation_id');
                $table->decimal('tax', 5, 2);
                $table->decimal('ratio', 5, 2);
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
        Schema::dropIfExists('corp_market_journal');
        Schema::dropIfExists('corp_market_structures');
    }
}