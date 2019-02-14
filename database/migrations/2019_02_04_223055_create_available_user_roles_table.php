<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('available_user_roles')) {
            Schema::create('available_user_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('role');
                $table->string('description');
                $table->timestamps();
            });

            DB::table('available_user_roles')->insert([
                'role' => 'None',
                'description' => 'User has no roles and is not allowed in the site.',
            ]);

            DB::table('available_user_roles')->insert([
                'role' => 'Guest',
                'description' => 'Guest of the site.',
            ]);

            DB::table('available_user_roles')->insert([
                'role' => 'Renter',
                'description' => 'Renters of W4RP.',
            ]);

            DB::table('available_user_roles')->insert([
                'role' => 'User',
                'description' => 'Members of Legacy allowed on the site.',
            ]);

            DB::table('available_user_roles')->insert([
                'role' => 'Admin',
                'description' => 'Admin of the site.',
            ]);

            DB::table('available_user_roles')->insert([
                'role' => 'SuperUser',
                'description' => 'Super user of the site.',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('available_user_roles');
    }
}
