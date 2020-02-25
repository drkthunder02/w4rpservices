<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllianceMoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_moons')) {
            Schema::create('alliance_moons', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->unique();
                $table->string('Region');
                $table->string('System');
                $table->string('Planet');
                $table->string('Moon');
                $table->string('Corporation');
                $table->string('StructureName')->default('No Name');
                $table->string('FirstOre')->default('None');
                $table->integer('FirstQuantity')->default('0');
                $table->string('SecondOre')->default('None');
                $table->integer('SecondQuantity')->default('0');
                $table->string('ThirdOre')->default('None');
                $table->integer('ThirdQuantity')->default('0');
                $table->string('FourthOre')->default('None');
                $table->integer('FourthQuantity')->default('0');
                $table->string('Moon_Type');
                $table->enum('Available', [
                    'Available',
                    'Request Pending',
                    'Reserved',
                    'Deployed',
                ])->default('Available');
            });
        }

        if(!Schema::hasTable('alliance_moon_requests')) {
            Schema::create('alliance_moon_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('region');
                $table->string('system');
                $table->string('planet');
                $table->string('moon');
                $table->string('corporation_name');
                $table->string('corporation_ticker');
                $table->unsignedBigInteger('corporation_id');
                $table->string('requestor_name');
                $table->unsignedBigInteger('requestor_id');
                $table->string('approver_name')->nullable();
                $table->ungignedBigInteger('approver_id')->nullable();
                $table->enum('status', [
                    'Pending',
                    'Approved',
                    'Denied',
                ]);
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
        Schema::dropIfExists('alliance_moons');
        Schema::dropIfExists('alliance_moon_requests');
    }
}
