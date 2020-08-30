<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBlackListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('alliance_blacklist')) {
            Schema::table('alliance_blacklist', function(Blueprint $table) {
                $table->enum('validity', [
                    'Valid',
                    'Invalid',
                ])->default('Valid');
                $table->string('removed_by_id')->nullable();
                $table->string('removed_by_name')->nullable();
                $table->string('removed_notes')->nullable();
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
        Schema::table('alliance_blacklist', function(Blueprint $table) {
            $table->dropColumn([
                'validity',
                'removed_by_id',
                'removed_by_name',
                'removed_notes',
            ]);
        });
    }
}
