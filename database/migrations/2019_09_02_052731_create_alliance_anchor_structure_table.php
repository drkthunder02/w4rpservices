<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllianceAnchorStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_anchor_structure')) {
            Schema::create('alliance_anchor_structure', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('corporation_id');
                $table->string('corporation_name');
                $table->string('system');
                $table->enum('structure_size', [
                    'M',
                    'L',
                    'XL',
                ]);
                $table->enum('structure_type', [
                    'Flex',
                    'Citadel',
                    'Engineering',
                    'Refinery',
                ]);
                $table->dateTime('requested_drop_time');
                $table->unsignedInteger('requester_id');
                $table->string('requester');
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
        Schema::dropIfExists('alliance_anchor_structure');
    }
}
