<?php

use Illuminate\Database\Seeder;

class WikiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wiki_user')->insert([
            'login' => 'nologin',
            'pass' => 'nopass',
            'name' => 'nologin',
        ]);

        DB::table('wiki_groupnames')->insert([
            'id' => '1',
            'gname' => 'user',
        ]);

        DB::table('wiki_groupnames')->insert([
            'id' => '2',
            'gname' => 'it',
        ]);

        DB::table('wiki_groupnames')->insert([
            'id' => '3',
            'gname' => 'fc',
        ]);

        DB::table('wiki_groupnames')->insert([
            'id' => '4',
            'gname' => 'admin',
        ]);

        DB::table('wiki_member')->insert([
            'uid' => '1',
            'gid' => '1',
            'gropuname' => 'user',
        ]);
    }
}
