<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Log;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\RentalMoon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
use App\Models\Moon\AllianceMoon;
use App\Models\MoonRentals\AllianceRentalMoon;
use App\Models\Moon\AllianceMoonRequest;

//Library
use App\Library\Moons\MoonCalc;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

class MoonsAdminController extends Controller
{
    /**
     * Variable for the class
     */
    private $romans = [
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    ];

    /**
     * Constructor for the class
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function ImportMoonSurveyData(Request $request) {
        //Declare variables
        $added = 0;
        $updated = 0;
        $moon = null;
        $newMoon = false;
        $num = 0;
        $planet = null;
        $moonNumber = null;

        foreach(explode("\n", $request->input('data')) as $row) {
            $cols = explode("\t", $row);
            dd($cols);            
        }

        return redirect('/admin/dashboard')->with('success', 'Import done: ' . $added . ' moons added ' . $updated . ' moons updated.');
    }

    private function romanNumberToInteger($roman) {
        $result = 0;

        foreach($this->romans as $key => $value) {
            while(strpos($roman, $key) === 0) {
                $result += $value;
                $roman = substr($roman, strlen($key));
            }
        }

        return $result;
    }

    private function integerToRomanNumber($number) {
        $returnValue = '';
        while($number > 0) {
            foreach($this->romans as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }

    private function arrayToCsv(array $rows) {
        //Declare needed variable as text null
        $result = '';

        //Open the temp file
        $fp = fopen('php://temp', 'w');

        //Process the file
        foreach($rows as $fields) {
            fputcsv($fp, $fields, ';', '"');

        }

        //Go back to the beginning of the file
        rewind($fp);

        //Continue through the buffer until the end
        while(($buffer = fgets($fp, 4096)) !== false) {
            $result .= $buffer;
        }

        //Close the temp file
        fclose($fp);

        //Return the result
        return $result;
    }
}
