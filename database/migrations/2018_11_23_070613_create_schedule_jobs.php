<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('schedule_jobs')) {
            Schema::create('schedule_jobs', function(Blueprint $table) {
                $table->increments('id');
                $table->string('job_name');
                $table->string('job_state');
                $table->dateTime('system_time');
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
        Schema::dropIfExists('schedule_jobs');
    }
}
