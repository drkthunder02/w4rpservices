<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('jobs_statuses')) {
            Schema::create('jobs_statuses', function (Blueprint $table) {
                $table->increments('id');
                $table->string('job_name');
                $table->boolean('complete')->default(false);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('jobs_errors')) {
            Schema::create('jobs_errors', function(Blueprint $table) {
                $table->integer('job_id');
                $table->string('job_name');
                $table->text('error');
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
        Schema::dropIfExists('jobs_status');
        Schema::dropIfExists('jobs_errors');
    }
}
