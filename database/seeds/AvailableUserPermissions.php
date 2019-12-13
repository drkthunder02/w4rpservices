<?php

use Illuminate\Database\Seeder;

class AvailableUserPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('available_user_permissions')->insert([
            'permission' => 'structure.operator',
        ]);

        DB::table('available_user_permissions')->insert([
            'permission' => 'logistics.minion',
        ]);

    }
}
