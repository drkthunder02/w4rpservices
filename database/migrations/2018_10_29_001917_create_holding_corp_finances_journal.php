<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldingCorpFinancesJournal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('HoldingcorpFinancesJournal');
    }
}
