<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_rental_systems')) {
            Schema::create('alliance_rental_systems', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('contact_id');
                $table->string('contact_name');
                $table->unsignedBigInteger('corporation_id');
                $table->string('corporation_name');
                $table->unsignedBigInteger('system_id');
                $table->string('system_name');
                $table->double('rental_cost', 20, 2);
                $table->dateTime('paid_until');
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
        Schema::dropIfExists('alliance_rental_systems');
    }
}
