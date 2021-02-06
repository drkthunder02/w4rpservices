<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiningTaxTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alliance_mining_tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id');
            $table->string('character_name');
            $table->unsignedBigInteger('invoice_id');
            $table->float('invoice_amount');
            $table->dateTime('date_issued');
            $table->dateTime('date_due');
            $table->enum('status', [
                'Pending',
                'Paid',
                'Late',
                'Paid Late',
                'Deferred',
            ])->default('Pending');
            $table->timestamps();
        });

        Schema::create('alliance_mining_tax_observers', function (Blueprint $table) {
            $table->id();
            $table->dateTime('last_updated');
            $table->unsignedBigInteger('observer_id');
            $table->string('observer_type');
            $table->timestamps();
        });

        Schema::create('alliance_mining_tax_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id');
            $table->string('character_name');
            $table->dateTime('last_updated');
            $table->unsignedBigInteger('type_id');
            $table->string('ore_name');
            $table->unsignedBigInteger('quantity');
            $table->enum('invoiced', [
                'No',
                'Yes',
            ])->default('No');
            $table->timestamps();
        });

        Schema::create('alliance_mining_tax_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id');
            $table->string('character_name');
            $table->unsignedBigInteger('invoice_id');
            $table->float('invoice_amount');
            $table->float('payment_amount');
            $table->dateTime('payment_date');
            $table->enum('status', [
                'Pending',
                'Accepted',
                'Rejected',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mining_tax_tables');
    }
}
