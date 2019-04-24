<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contracts')) {
            Schema::create('contracts', function(Blueprint $table) {
                $table->increment('id')->unique();
                $table->string('title');
                $table->date('date');
                $table->text('body');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('contract_bids')) {
            Schema::create('contract_bids', function(Blueprint $table) {
                $table->increment('id')->unique();
                $table->integer('contract_id');
                $table->decimal('bid');
                $table->boolean('accepted');
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
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_bids');
    }
}
