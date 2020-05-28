<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllianceMoonRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('alliance_rental_moons')) {
            $table->bigIncrements('id');
            $table->string('region');
            $table->string('system');
            $table->string('planet');
            $table->string('moon');
            $table->unsignedBigInteger('structure_id')->default(0);
            $table->string('structure_name')->default('No Name');
            $table->string('first_ore')->default('None');
            $table->decimal('first_quantity')->default(0.00);
            $table->string('second_ore')->default('None');
            $table->decimal('second_quantity')->default(0.00);
            $table->string('third_ore')->default('None');
            $table->decimal('third_quantity')->default(0.00);
            $table->string('fourth_ore')->default('None');
            $table->decimal('fourth_quantity')->default(0.00);
            $table->decimal('moon_worth', 17, 2)->default(0.00);
            $table->decimal('alliance_rental_price', 17,2)->default(0.00);
            $table->decimal('out_of_alliance_rental_price', 17,2)->default(0.00);
            $table->enum('rental_type', [
                'Not Rented',
                'In Alliance',
                'Out of Alliance',
                'Alliance',
            ])->default('Not Rented');
            $table->dateTime('rental_until')->nullable();
            $table->unsignedBigInteger('rental_contact_id')->default(0);
            $table->enum('rental_contact_type', [
                'Player',
                'Corporation',
                'Alliance',
                'Unknown',
                'Not Rented',
            ])->default('Not Rented');
            $table->enum('paid' ,[
                'Yes',
                'No',
                'Not Rented',
            ])->default('Not Rented');
            $table->dateTime('paid_until')->nullable();
            $table->dateTime('alliance_use_until')->nullable();
            $table->timestamps();
        }

        //Transfer the existing data into the table

        //Drop the older tables
        Schema::dropIfExists('moon_rents');
        Schema::dropIfExists('RentalMoons');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alliance_rental_moons');
    }
}
