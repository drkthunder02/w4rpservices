<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiningTaxMiningOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_mining_tax_operations')) {
            Schema::create('alliance_mining_tax_operations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('structure_id');
                $table->string('structure_name');
                $table->unsignedBigInteger('authorized_by_id');
                $table->string('authorized_by_name');
                $table->date('operation_date');
                $table->string('operation_name');
                $table->enum('processed', [
                    'No',
                    'Yes',
                ])->default('No');
                $table->date('processed_on')->nullable();
                $table->timestamps();
            })
        }

        if(!Schema::hasTable('alliance_mining_tax_invoices')) {
            Schema::table('alliance_mining_tax_invoices', function (Blueprint $table) {
                $table->unsignedBigInteger('modified_by_id')->nullable();
                $table->string('modified_by_name')->nullable();
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
        Schema::dropIfExists('alliance_mining_tax_operations');
    }
}
