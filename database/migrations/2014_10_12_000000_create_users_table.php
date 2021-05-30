<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->unsignedBigInteger('character_id')->unique();
                $table->string('avatar');
                $table->string('access_token')->nullable();
                $table->string('refresh_token')->nullable();
                $table->integer('inserted_at')->default(0);
                $table->integer('expires_in')->default(0);
                $table->string('owner_hash');
                $table->string('user_type')->default('Guest');
                $table->text('scopes')->nullable();
                $table->string('email')->unique()->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('user_alts')) {
            Schema::create('user_alts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->unsignedBigInteger('main_id');
                $table->unsignedBigInteger('character_id')->unique();
                $table->string('avatar');
                $table->string('access_token')->nullable();
                $table->string('refresh_token')->nullable();
                $table->integer('inserted_at')->default(0);
                $table->integer('expires_in')->default(0);
                $table->string('owner_hash');
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('main_id', 'fk_main_id')
                      ->references('character_id')
                      ->on('users')
                      ->cascadeOnDelete();
            });
        }

        if(!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('character_id');
                $table->string('role')->default('None');
                $table->timestamps();

                $table->foreign('character_id', 'fk_character_id')
                      ->references('character_id')
                      ->on('users')
                      ->cascadeOnDelete();
            });
        }

        if(!Schema::hasTable('EsiTokens')) {
            Schema::create('EsiTokens', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('character_id')->unique();
                $table->string('access_token');
                $table->string('refresh_token');
                $table->integer('expires_in');
                $table->timestamps();
           });
        }

        if(!Schema::hasTable('EsiScopes')) {
            Schema::create('EsiScopes', function(Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('character_id');
                $table->string('scope');
                $table->timestamps();

                $table->foreign('character_id', 'fk_character_id')
                      ->references('character_id')
                      ->on('EsiTokens')
                      ->cascadeOnDelete();

            });
        }

        if(!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('character_id');
                $table->string('permission');
                $table->timestamps();

                $table->foreign('character_id', 'fk_character_id')
                      ->references('character_id')
                      ->on('users')
                      ->cascadeOnDelete();
            });
        }

        if(!Schema::hasTable('available_user_permissions')) {
            Schema::create('available_user_permissions', function(Blueprint $table) {
                $table->increments('id');
                $table->string('permission');
            });
        }

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

        if(!Schema::hasTable('user_to_corporation')) {
            Schema::create('user_to_corporation', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
            });
        }

        if(!Schema::hasTable('AllianceCorps')) {
            Schema::create('AllianceCorps', function(Blueprint $table) {
                $table->integer('corporation_id')->unique();
                $table->string('name');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('character_to_corporation')) {
            Schema::create('character_to_corporation', function (Blueprint $table) {
                $table->increments('id');
                $table->string('character_id');
                $table->string('character_name');
                $table->string('corporation_id');
                $table->string('corporation_name');
            });
        }

        if(!Schema::hasTable('corporation_to_alliance')) {
            Schema::create('corporation_to_alliance', function (Blueprint $table) {
                $table->increments('id');
                $table->string('corporation_id');
                $table->string('corporation_name');
                $table->string('alliance_id');
                $table->string('alliance_name');
            });
        }

        if(!Schema::hasTable('allowed_logins')) {
            Schema::create('allowed_logins', function(Blueprint $table) {
                $table->increments('id');
                $table->string('entity_id');
                $table->string('entity_type');
                $table->string('entity_name');
                $table->string('login_type');
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_alts');
        Schema::dropIfExists('available_user_roles');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('EsiTokens');
        Schema::dropIfExists('EsiScopes');
        Schema::dropIfExists('available_user_permissions');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_to_corporation');
        Schema::dropIfExists('AllianceCorps');
        Schema::dropIfExists('character_to_corporation');
        Schema::dropIfExists('corporation_to_alliance');
        Schema::dropIfExists('allowed_logins');
    }
}
