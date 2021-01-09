<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAllianceMoonRentalInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alliance_moon_rental_invoices', function (Blueprint $table) {
            $table->bigInteger('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alliance_moon_rental_invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
}
