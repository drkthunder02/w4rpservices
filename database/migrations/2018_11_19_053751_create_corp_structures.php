<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorpStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('CorpStructures')) {
            Schema::create('CorpStructures', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('character_id');
                $table->integer('corporation_id');
                $table->string('corporation_name');
                $table->string('region');
                $table->string('system');
                $table->string('structure_name');
                $table->decimal('tax', 10, 2);
                $table->string('structure_type');
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
        Schema::dropIfExists('CorpStructures');
    }
}
