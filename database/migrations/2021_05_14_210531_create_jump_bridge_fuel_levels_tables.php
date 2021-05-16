<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJumpBridgeFuelLevelsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('alliance_flex_structures');
        Schema::dropIFExists('alliance_assets');
        Schema::dropIfExists('alliance_structures');
        Schema::dropIfExists('alliance_services');

        if(!Schema::hasTable('alliance_structures')) {
            Schema::create('alliance_structures', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('structure_id')->unique();
                $table->string('structure_name');
                $table->unsignedBigInteger('solar_system_id');
                $table->string('solar_system_name')->nullable();
                $table->unsignedBigInteger('type_id');
                $table->unsignedBigInteger('corporation_id');
                $table->boolean('services');
                $table->enum('state'. [
                    'anchor_vulnerable', 
                    'anchoring', 
                    'armor_reinforce', 
                    'armor_vulnerable', 
                    'deploy_vulnerable', 
                    'fitting_invulnerable', 
                    'hull_reinforce', 
                    'hull_vulnerable', 
                    'online_deprecated', 
                    'onlining_vulnerable', 
                    'shield_vulnerable', 
                    'unanchored', 
                    'unknown',
                ]);
                $table->dateTime('state_timer_start')->nullable();
                $table->dateTime('state_timer_end')->nullable();
                $table->dateTime('fuel_expires')->nullable();
                $table->unsignedBigInteger('profile_id');
                $table->dateTime('next_reinforce_apply')->nullable();
                $table->unsignedInteger('next_reinforce_hour')->nullable();
                $table->unsignedInteger('reinforce_hour');
                $table->dateTime('unanchors_at')->nullable();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_services')) {
            Schema::create('alliance_services', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('structure_id');
                $table->string('name');
                $table->enum('state', [
                    'online', 
                    'offline', 
                    'cleanup',
                ]);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('alliance_assets')) {
            Schema::create('alliance_assets', function(Blueprint $table) {
                $table->increments('id');
                $table->boolean('is_blueprint_copy')->nullable();
                $table->boolean('is_singleton');
                $table->unsignedBigInteger('item_id');
                $table->enum('location_flag', [
                    'AssetSafety', 
                    'AutoFit', 
                    'Bonus', 
                    'Booster', 
                    'BoosterBay', 
                    'Capsule', 
                    'Cargo', 
                    'CorpDeliveries', 
                    'CorpSAG1', 
                    'CorpSAG2', 
                    'CorpSAG3', 
                    'CorpSAG4', 
                    'CorpSAG5', 
                    'CorpSAG6', 
                    'CorpSAG7', 
                    'CrateLoot', 
                    'Deliveries', 
                    'DroneBay', 
                    'DustBattle', 
                    'DustDatabank', 
                    'FighterBay', 
                    'FighterTube0', 
                    'FighterTube1', 
                    'FighterTube2', 
                    'FighterTube3', 
                    'FighterTube4', 
                    'FleetHangar', 
                    'FrigateEscapeBay', 
                    'Hangar', 
                    'HangarAll', 
                    'HiSlot0', 
                    'HiSlot1', 
                    'HiSlot2', 
                    'HiSlot3', 
                    'HiSlot4', 
                    'HiSlot5', 
                    'HiSlot6', 
                    'HiSlot7', 
                    'HiddenModifiers', 
                    'Implant', 
                    'Impounded', 
                    'JunkyardReprocessed', 
                    'JunkyardTrashed', 
                    'LoSlot0', 
                    'LoSlot1', 
                    'LoSlot2', 
                    'LoSlot3', 
                    'LoSlot4', 
                    'LoSlot5', 
                    'LoSlot6', 
                    'LoSlot7', 
                    'Locked', 
                    'MedSlot0', 
                    'MedSlot1', 
                    'MedSlot2', 
                    'MedSlot3', 
                    'MedSlot4', 
                    'MedSlot5', 
                    'MedSlot6', 
                    'MedSlot7', 
                    'OfficeFolder', 
                    'Pilot', 
                    'PlanetSurface', 
                    'QuafeBay', 
                    'QuantumCoreRoom', 
                    'Reward', 
                    'RigSlot0', 
                    'RigSlot1', 
                    'RigSlot2', 
                    'RigSlot3', 
                    'RigSlot4', 
                    'RigSlot5', 
                    'RigSlot6', 
                    'RigSlot7', 
                    'SecondaryStorage', 
                    'ServiceSlot0', 
                    'ServiceSlot1', 
                    'ServiceSlot2', 
                    'ServiceSlot3', 
                    'ServiceSlot4', 
                    'ServiceSlot5', 
                    'ServiceSlot6', 
                    'ServiceSlot7', 
                    'ShipHangar', 
                    'ShipOffline', 
                    'Skill', 
                    'SkillInTraining', 
                    'SpecializedAmmoHold', 
                    'SpecializedCommandCenterHold', 
                    'SpecializedFuelBay', 
                    'SpecializedGasHold', 
                    'SpecializedIndustrialShipHold', 
                    'SpecializedLargeShipHold', 
                    'SpecializedMaterialBay', 
                    'SpecializedMediumShipHold', 
                    'SpecializedMineralHold', 
                    'SpecializedOreHold', 
                    'SpecializedPlanetaryCommoditiesHold', 
                    'SpecializedSalvageHold', 
                    'SpecializedShipHold', 
                    'SpecializedSmallShipHold', 
                    'StructureActive', 
                    'StructureFuel', 
                    'StructureInactive', 
                    'StructureOffline', 
                    'SubSystemBay', 
                    'SubSystemSlot0', 
                    'SubSystemSlot1', 
                    'SubSystemSlot2', 
                    'SubSystemSlot3', 
                    'SubSystemSlot4', 
                    'SubSystemSlot5', 
                    'SubSystemSlot6', 
                    'SubSystemSlot7', 
                    'Unlocked', 
                    'Wallet', 
                    'Wardrobe',
                ]);
                $table->unsignedBigInteger('location_id');
                $table->enum('location_type', [
                    'station',
                    'solar_system',
                    'item',
                    'other',
                ]);
                $table->unsignedBigInteger('quantity');
                $table->unsignedBigInteger('type_id');
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
        Schema::dropIfExists('fleet_activity_tracking');
        Schema::dropIfExists('alliance_structures');
        Schema::dropIfExists('alliance_services');
        Schema::dropIfExists('alliance_assets');
    }
}
