<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemLookupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('item_lookup')) {
            Schema::create('item_lookup', function (Blueprint $table) {
                $table->double('capacity', 20, 2)->nullable();
                $table->string('description');
                $table->unsignedBigInteger('graphic_id')->nullable();
                $table->unsignedBigInteger('group_id');
                $table->unsignedBigInteger('icon_id')->nullable();
                $table->unsignedBigInteger('market_group_id')->nullable();
                $table->double('mass', 20, 2)->nullable();
                $table->string('name');
                $table->double('packaged_volume', 20, 2)->nullable();
                $table->unsignedBigInteger('portion_size')->nullable();
                $table->boolean('published');
                $table->double('radius', 20, 2)->nullable();
                $table->unsignedBigInteger('type_id')->unique();
                $table->double('volume', 20, 2)->nullable();
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
        Schema::dropIfExists('item_lookup');
    }
}
