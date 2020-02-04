<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlexStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_flex_structures')) {
            Schema::create('alliance_flex_structures', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('requestor_id');
                $table->string('requestor_name');
                $table->unsignedBigInteger('requestor_corp_id');
                $table->string('requestor_corp_name');
                $table->unsignedBigInteger('system_id');
                $table->string('system');
                $table->enum('structure_type', [
                    'Cyno Jammer',
                    'Cyno Beacon',
                    'Jump Bridge',
                    'Super Construction Facilities',
                ]);
                $table->double('structure_cost', 20, 2);
                $table->dateTime('paid_until');
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
        Schema::dropIfExists('flex_structures');
    }
}
