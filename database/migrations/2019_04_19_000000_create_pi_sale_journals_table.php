<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePiSaleJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::dropIfExists('pi_sale_journal');
    }
}
