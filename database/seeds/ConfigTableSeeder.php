<?php

use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Config')->insert([
            'RentalTax' => 15.00,
            'AllyRentalTax' => 20.00,
            'RefineRate' => 84.00,
            'RentalTime' => 2592000,
        ]);
    }
}
