<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('Structures')) {
            Schema::create('Structures', function(Blueprint $table) {
                $table->integer('corporation_id');
                $table->dateTime('fuel_expires');
                $table->dateTime('next_reinforce_apply');
                $table->integer('next_reinforce_hour');
                $table->integer('next_reinforce_weekday');
                $table->integer('profile_id');
                $table->integer('reinforce_hour');
                $table->integer('reinforce_weekday');
                $table->dateTime('services');
                $table->string('state');
                $table->date('state_timer_end');
                $table->date('state_timer_start');
                $table->integer('structure_id')->unique();
                $table->integer('system_id');
                $table->integer('type_id');
                $table->dateTime('unanchors_at');
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
        Schema::dropIfExists('Structures');
    }
}
