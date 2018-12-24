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

        DB::table('available_user_permissions')->insert([
            'permission' => 'helpdesk.diplomat',
        ]);

        DB::table('available_user_permissions')->insert([
            'permission' => 'helpdesk.moonadmin',
        ]);

        DB::table('available_user_permissions')->insert([
            'permission' => 'helpdesk.fleetcommand',
        ]);

        DB::table('available_user_permissions')->insert([
            'permission' => 'helpdesk.leadership',
        ]);
    }
}
