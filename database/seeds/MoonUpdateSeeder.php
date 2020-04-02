<?php

//Internal Libraries
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

//Models
use App\Models\Moon\AllianceMoon;
use App\Models\Moon\Moon;

class MoonUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->UpdateAllianceMoons();
        $this->UpdateRentalMoons();
    }

    private function IsRMoon($firstOre, $secondOre, $thirdOre, $fourthOre) {
        $rMoons = [
            'Carnotite',
            'Zircon',
            'Pollucite',
            'Cinnabar',
            'Xenotime',
            'Monazite',
            'Loparite',
            'Ytterbite',
        ];

        if(in_array($firstOre, $rMoons) || in_array($secondOre, $rMoons) || in_array($thirdOre, $rMoons) || in_array($fourthOre, $rMoons)) {
            return true;
        } else {
            return false;
        }
    }

    private function FindRegion($system) {
        $catch = [
            '6X7-JO',
            'A-803L',
            'I-8D0G',
            'WQH-4K',
            'GJ0-OJ',
            'JWZ2-V',
            'J-ODE7',
            'OGL8-Q',
            'R-K4QY',
            'Q-S7ZD'
        ];

        $immensea = [
            'ZBP-TP',
            'DY-P7Q',
            'XVV-21',
            '78TS-Q',
            'GXK-7F',
            'CJNF-J',
            'EA-HSA',
            'FYI-49',
            'WYF8-8',
            'B9E-H6',
            'JDAS-0',
            'Y19P-1',
            'LN-56V',
            'O7-7UX',
            'Y2-QUV',
            'SPBS-6',
            'A4B-V5',
            'NS2L-4',
            'AF0-V5',
            'B-S347',
            'PPFB-U',
            'B-A587',
            'QI-S9W',
            'L-5JCJ',
            '4-GB14',
            'REB-KR',
            'QE-E1D',
            'LK1K-5',
            'Z-H2MA',
            'B-KDOZ',
            'E8-YS9',
        ];

        if(in_array($system, $catch)) {
            return 'Catch';
        } else if(in_array($system, $immensea)) {
            return 'Immensea';
        } else {
            return null;
        }
    }

    private function UpdateRentalMoons() {
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

        foreach($lines as $line) {
            //If the moon is a rare moon, then either update it or add it.
            if($this->IsRMoon($line[3], $line[5], $line[7], $line[9])) {
                $count = Moon::where([
                    'System' => $line[0],
                    'Planet' =>  $line[1],
                    'Moon' => $line[2],
                ])->count();
                //Insert the moon into the database
                if($count == 0) {
                    $region = $this->FindRegion($line[0]);

                    Moon::insert([
                        'Region' => $region,
                        'System' => $line[0],
                        'Planet' => $line[1],
                        'Moon' => $line[2],
                        'StructureName' => 'No Name',
                        'FirstOre' => $line[3],
                        'FirstQuantity' => $line[4],
                        'SecondOre' => $line[5],
                        'SecondQuantity' => $line[6],
                        'ThirdOre' => $line[7],
                        'ThirdQuantity' => $line[8],
                        'FourthOre' => $line[9],
                        'FourthQuantity' => $line[10],
                    ]);
                } else {  //If the moon is found then update it.
                    AllianceMoon::where([
                        'System' => $line[0],
                        'Planet' => $line[1],
                        'Moon' => $line[2],
                    ])->update([
                        'FirstOre' => $line[3],
                        'FirstQuantity' => $line[4],
                        'SecondOre' => $line[5],
                        'SecondQuantity' => $line[6],
                        'ThirdOre' => $line[7],
                        'ThirdQuantity' => $line[8],
                        'FourthOre' => $line[9],
                        'FourthQuantity' => $line[10],
                    ]);
                }

            }
        }
    }

    private function UpdateAllianceMoons() {
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

        foreach($lines as $line) {
            //Update the alliance moons
            AllianceMoon::where([
                'System' => $line[0],
                'Planet' => $line[1],
                'Moon' => $line[2],
            ])->update([
                'FirstOre' => $line[3],
                'FirstQuantity' => $line[4],
                'SecondOre' => $line[5],
                'SecondQuantity' => $line[6],
                'ThirdOre' => $line[7],
                'ThirdQuantity' => $line[8],
                'FourthOre' => $line[9],
                'FourthQuantity' => $line[10],
            ]);
        }
    }
}
