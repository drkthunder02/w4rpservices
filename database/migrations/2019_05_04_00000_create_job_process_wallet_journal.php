<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobProcessWaletJournal extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function up() {
        if(!Schema::hasTable('job_process_wallet_journal')) {
            Schema::create('job_process_wallet_journal', function(Blueprint $table) {
                $table->increments('id')->unique();
                $table->string('charId');
                $table->string('division');
                $table->integer('page');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function down() {
        Schema::dropIfExists('job_process_wallet_journal');
    }
}

?>