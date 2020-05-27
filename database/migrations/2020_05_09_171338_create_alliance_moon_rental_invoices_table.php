<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllianceMoonRentalInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_moon_rental_invoices')) {
            Schema::create('alliance_moon_rental_invoices', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('invoice_id')->unique();
                $table->unsignedBigInteger('character_id');
                $table->string('character_name');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->text('rental_moons')->nullalbe();
                $table->decimal('invoice_amount', 17, 2);
                $table->dateTime('due_date');
                $table->enum('paid', ['Yes', 'No'])->default('No');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_moon_rental_payments')) {
            Schema::create('alliance_moon_rental_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('invoice_id')->unique();
                $table->decimal('payment_amount');
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
        Schema::dropIfExists('alliance_moon_rental_invoices');
        Schema::dropIfExists('alliance_moon_rental_payments');
    }
}
