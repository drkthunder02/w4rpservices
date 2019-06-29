<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrpTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('srp_ships')) {
            Schema::create('srp_ships', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id')->default('N/A');
                $table->string('character_name');
                $table->string('fleet_commander_id');
                $table->string('fleet_commander_name');
                $table->string('zkillboard');
                $table->string('ship_type');
                $table->decimal('loss_value', 20, 2);
                $table->string('approved')->default('Not Paid');
                $table->decimal('paid_value', 20, 2)->default(0.00);
                $table->string('notes')->nullable();
                $table->string('paid_by_id')->nullable();
                $table->string('paid_by_name')->nullable();
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
        Schema::dropIfExists('srp_ships');
    }
}
