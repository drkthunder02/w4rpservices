<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfterActionReportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('fc_after_action_reports')) {
            Schema::create('fc_after_action_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fc_id');
                $table->string('fc_name');
                $table->dateTime('formup_time');
                $table->string('formup_location');
                $table->enum('comms', [
                    'W4RP',
                    'Brave',
                    'TEST',
                    'Other',
                ]);
                $table->string('doctrine');
                $table->text('objective');
                $table->enum('objective_result', [
                    'Win',
                    'Lose',
                    'Neither',
                ]);
                $table->text('summary');
                $table->text('improvements');
                $table->text('worked_well');
                $table->text('additonal_comments');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('fc_aar_comments')) {
            Schema::create('fc_aar_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('report_id');
                $table->unsignedBigInteger('character_id');
                $table->unsignedBigInteger('character_name');
                $table->text('comments');
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
        Schema::dropIfExists('fc_aar_comments');
        Schema::dropIfExists('fc_after_action_reports');
    }
}
