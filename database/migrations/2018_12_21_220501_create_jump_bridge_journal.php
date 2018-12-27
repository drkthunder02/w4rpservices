<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJumpBridgeJournal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jump_bridge_journal');
    }
}
