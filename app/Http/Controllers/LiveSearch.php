<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class LiveSearch extends Controller
{
    public function index()  {
        return view('ajax.live_search');
    }

    public function action(Request $request) {
        if($request->ajax()) {
            $query = $request->get('query');
            if($query != '') {
                $data = User::where('name', 'like', '%' . $query . '%')->get();
            } else {
                $data =  User::all()->orderBy('name', 'desc')->get();
            }

            $total_row = $data->count();
            if($total_row > 0) {
                foreach($data as $row) {
                    $output .= '
                    <tr>
                        <td>' . $row->name . '</td>
                    </tr>
                    ';
                }
            } else {
                $output = '
                <tr>
                    <td align="center" colspann="5">No Data Found</td>
                </tr>                
                ';
            }

            $data = array(
                'table_data' => $output,
                'total_data' => $total_data,
            );

            echo json_encode($data);
        }
    }
}
