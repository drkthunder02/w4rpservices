<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Models\User\User;

class LiveSearch extends Controller
{
    public function index()  {
        return view('ajax.live_search');
    }

    public function action(Request $request) {
        if($request->ajax()) {
            $output = '';
            $query = $request->get('query');
            if($query != null) {
                $data = User::where('name', 'like', '%'.$query.'%')->get();
            } else {
                $data = User::all();
            }
            $total_row = $data->count();
            if($total_row > 0 ) {
                foreach($data as $row) {
                    $output .= '
                        <tr>
                        <td>'.$row->name.'</td>
                        <td>'.$row->character_id.'</td>
                        </tr>';
                }
            } else {
                $output = '<tr><td align="center" colspan="5">No Data Found</td></tr>';
            }
            $data = array('table_data' => $output, 'total_data' => $total_row);

            echo json_encode($data);
        }
    }
}
