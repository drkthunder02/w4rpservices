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
        Schema::table('user_alts', function(Blueprint $table) {
            $table->foreign('main_id')
                  ->references('character_id')
                  ->on('users')
                  ->cascadeOnDelete();
        });

        Schema::table('user_roles', function(Blueprint $table) {
            $table->foreign('character_id')
                  ->references('character_id')
                  ->on('users')
                  ->cascadeOnDelete();
        });

        Schema::table('EsiScopes', function(Blueprint $table) {
            $table->foreign('character_id')
                  ->references('character_id')
                  ->on('EsiTokens')
                  ->cascadeOnDelete();
        });

        Schema::table('user_permissions', function(Blueprint $table) {
            $table->foreign('character_id')
                  ->references('character_id')
                  ->on('users')
                  ->cascadeOnDelete();
        });

        Schema::table('supply_chain_bids', function(Blueprint $table) {
            $table->foreign('contract_id')
                  ->references('contract_id')
                  ->on('supply_chain_contracts')
                  ->cascadeOnDelete();
        });

        Schema::table('alliance_mining_tax_ledgers', function(Blueprint $table) {
            $table->foreign('observer_id')
                  ->references('observer_id')
                  ->on('alliance_mining_tax_observers')
                  ->cascadeOnDelete();
        });

        Schema::table('alliance_mining_tax_payments', function(Blueprint $table) {
            $table->foreign('invoice_id')
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
