<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogisticsContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('logistics_contracts')) {
            Schema::create('logistics_contracts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('shipper_id')->nullable();
                $table->string('shipper_name')->nullable();
                $table->integer('accepted_by_id')->nullable();
                $table->string('accepted_by_name')->nullable();
                $table->integer('start_system_id')->nullable();
                $table->string('start_system_name')->nullable();
                $table->integer('destination_system_id')->nullable();
                $table->string('destination_system_name')->nullable();
                $table->decimal('price', 20, 2)->default(0.00);
                $table->decimal('collateral', 20, 2)->default(0.00);
                $table->boolean('insured')->default('false');
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
        Schema::dropIfExists('logistics_contracts');
    }
}
