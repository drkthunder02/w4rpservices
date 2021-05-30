<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToExistingMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alliance_mining_tax_ledgers', function(Blueprint $table) {
            $table->foreign('observer_id', 'fk_alliance_mining_observer_id')
                  ->references('observer_id')
                  ->on('alliance_mining_tax_observers')
                  ->cascadeOnDelete();
        });

        Schema::table('alliance_mining_tax_payments', function(Blueprint $table) {
            $table->foreign('invoice_id', 'fk_alliance_mining_invoice_id')
                  ->references('invoice_id')
                  ->on('alliance_mining_tax_invoices')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
