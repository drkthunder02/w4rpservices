<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllianceJournalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_wallet_journal')) {
            Schema::create('alliance_wallet_journal', function (Blueprint $table) {
                $table->string('id')->unique();
                $table->unsignedBigInteger('corporation_id');
                $table->unsignedInteger('division');
                $table->decimal('amount', 20, 2)->nullable();
                $table->decimal('balance', 20, 2)->nullable();
                $table->unsignedBigInteger('context_id')->nullable();
                $table->enum('context_id_type', [
                    'structure_id',
                    'station_id',
                    'market_transaction_id',
                    'character_id',
                    'corporation_id',
                    'alliance_id',
                    'eve_system',
                    'industry_job_id',
                    'contract_id',
                    'planet_id',
                    'system_id',
                    'type_id',
                ]);
                $table->dateTime('date')->nullable();
                $table->string('description')->nullable();
                $table->unsignedBigInteger('first_party_id')->nulalble();
                $table->string('reason')->nullable();
                $table->enum('ref_type', [
                    'acceleration_gate_fee',
                    'advertisement_listing_fee',
                    'agent_donation',
                    'agent_location_services',
                    'agent_miscellaneous',
                    'agent_mission_collateral_paid',
                    'agent_mission_collateral_refunded',
                    'agent_mission_reward',
                    'agent_mission_reward_corporation_tax',
                    'agent_mission_time_bonus_reward',
                    'agent_mission_time_bonus_reward_corporation_tax',
                    'agent_security_services',
                    'agent_services_rendered',
                    'agents_preward',
                    'alliance_maintainance_fee',
                    'alliance_registration_fee',
                    'asset_safety_recovery_tax',
                    'bounty',
                    'bounty_prize',
                    'bounty_prize_corporation_tax',
                    'bounty_prizes',
                    'bounty_reimbursement',
                    'bounty_surcharge',
                    'brokers_fee',
                    'clone_activation',
                    'clone_transfer',
                    'contraband_fine',
                    'contract_auction_bid',
                    'contract_auction_bid_corp',
                    'contract_auction_bid_refund',
                    'contract_auction_sold',
                    'contract_brokers_fee',
                    'contract_brokers_fee_corp',
                    'contract_collateral',
                    'contract_collateral_deposited_corp',
                    'contract_collateral_payout',
                    'contract_collateral_refund',
                    'contract_deposit',
                    'contract_deposit_corp',
                    'contract_deposit_refund',
                    'contract_deposit_sales_tax',
                    'contract_price',
                    'contract_price_payment_corp',
                    'contract_reversal',
                    'contract_reward',
                    'contract_reward_deposited',
                    'contract_reward_deposited_corp',
                    'contract_reward_refund',
                    'contract_sales_tax',
                    'copying',
                    'corporate_reward_payout',
                    'corporate_reward_tax',
                    'corporation_account_withdrawal',
                    'corporation_bulk_payment',
                    'corporation_dividend_payment',
                    'corporation_liquidation',
                    'corporation_logo_change_cost',
                    'corporation_payment',
                    'corporation_registration_fee',
                    'courier_mission_escrow',
                    'cspa',
                    'cspaofflinerefund',
                    'datacore_fee',
                    'dna_modification_fee',
                    'docking_fee',
                    'duel_wager_escrow',
                    'duel_wager_payment',
                    'duel_wager_refund',
                    'ess_escrow_transfer',
                    'factory_slot_rental_fee',
                    'gm_cash_transfer',
                    'industry_job_tax',
                    'infrastructure_hub_maintenance',
                    'inheritance',
                    'insurance',
                    'item_trader_payment',
                    'jump_clone_activation_fee',
                    'jump_clone_installation_fee',
                    'kill_right_fee',
                    'lp_store',
                    'manufacturing',
                    'market_escrow',
                    'market_fine_paid',
                    'market_transaction',
                    'medal_creation',
                    'medal_issued',
                    'mission_completion',
                    'mission_cost',
                    'mission_expiration',
                    'mission_reward',
                    'office_rental_fee',
                    'operation_bonus',
                    'opportunity_reward',
                    'planetary_construction',
                    'planetary_export_tax',
                    'planetary_import_tax',
                    'player_donation',
                    'player_trading',
                    'project_discovery_reward',
                    'project_discovery_tax',
                    'reaction',
                    'release_of_impounded_property',
                    'repair_bill',
                    'reprocessing_tax',
                    'researching_material_productivity',
                    'researching_technology',
                    'researching_time_productivity',
                    'resource_wars_reward',
                    'reverse_engineering',
                    'security_processing_fee',
                    'shares',
                    'skill_purchase',
                    'sovereignity_bill',
                    'store_purchase',
                    'store_purchase_refund',
                    'structure_gate_jump',
                    'transaction_tax',
                    'upkeep_adjustment_fee',
                    'war_ally_contract',
                    'war_fee',
                    'war_fee_surrender',
                ]);
                $table->unsignedBigInteger('second_party_id')->nullable();
                $table->decimal('tax', 20, 2)->default(0.00);
                $table->unsignedBigInteger('tax_receiver_id')->nullable();
                $table->timestamps();
            });
        }

        //Drop the old tables which we don't want to use anymore
        Schema::dropIfExists('CorpJournals');
        Schema::dropIfExists('HoldingCorpFinancesJournal');
        Schema::dropIfExists('jump_bridge_journal');
        Schema::dropIfExists('player_donation_journal');
        Schema::dropIfExists('reprocessing_tax_journal');
        Schema::dropIfExists('office_fees_journal');
        Schema::dropIfExists('structure_industry_tax_journal');
        Schema::dropIfExists('planet_production_tax_journal');
        Schema::dropIfExists('monthly_market_taxes');
        Schema::dropIfExists('pi_sale_journal');
        Schema::dropIfExists('alliance_market_journal');
        Schema::dropIfExists('corp_market_journal');
        Schema::dropIfExists('corp_market_structures');
        Schema::dropIfExists('sov_bill_journal');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alliance_journal');
    }
}
