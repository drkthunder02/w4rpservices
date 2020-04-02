<?php

//Internal Libraries
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

//Models
use App\Models\Moon\AllianceMoon;

class MoonUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->Updater();
    }

    private function Updater() {
        $lines = array();

        //Create the file handler
        $data = Storage::get('public/moon_data.txt');
        //Split the string into separate arrays based on the line
        $data = preg_split("/\n/", $data);

        //For each array of data, let's separate the data into more arrays built in arrays
        for($i = 0; $i < sizeof($data); $i++) {
            //Strip the beginning [ from the line
            $temp = str_replace('[', '', $data[$i]);
            //Strip the ending ] from the line
            $temp = str_replace(']', '', $temp);
            //Remove the spacees from the line
            $temp = str_replace(' ', '', $temp);
            //Remove the quotes from the line
            $temp = str_replace("'", '', $temp);
            //Split up the line into separate arrays after each comma
            $lines[$i] = preg_split("/,/", $temp);
        }

        /**
         * The output within the lines array
         * 0 => System
         * 1 => Planet
         * 2 => Moon
         * 3 => FirstOre
         * 4 => FirstQuan
         * 5 => SecondOre
         * 6 => SecondQuan
         * 7 => ThirdOre
         * 8 => ThirdQuan
         * 9 => FourthOre
         * 10 => FourthQuan
         */

    }
}
