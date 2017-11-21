<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_position;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Position extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage position-----------------------------------------------------------------

        // manage position index function---------------------------------------------------
        public function index_pos()
        {
            // query left join
            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Position';
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            // return to view
            return view('mainmenu/position/poshome', ['ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

        // manage position add function-----------------------------------------------------
        public function add_pos()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Position';
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            return view('mainmenu/position/posadd');
        }
        // ---------------------------------------------------------------------------------

        // manage position do add function--------------------------------------------------
        public function do_add_pos(Request $request)
        {
            $this->validate($request, [
                'name'       => 'required|max:100|unique:ms_jabatan'
            ]);

            $data = new manage_position;
            $data->name             = $request->name;
            $data->created_date     = date('Y-m-d H:i:s');
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Position';
                $table_logs  = 'ms_jabatan';
                $id_logs     = $data->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($request->all());

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->table               = $table_logs;
                $data->primary             = $id_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;
                $data->param               = $param_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            return redirect(url('mainmenu/pos_home'))->with('status', ' Created new position has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage position edit function----------------------------------------------------
        public function edit_pos($id)
        {
            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Position';
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;
                $data->primary             = $id;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            return view('mainmenu/position/posedit', ['ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

        // manage position do edit function-------------------------------------------------
        public function do_edit_pos(Request $request, $id)
        {
            $this->validate($request, [
                'name'       => 'required|max:100'
            ]);

            $data = manage_position::find($id);
            $data->name             = $request->name;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Position';
                $table_logs  = 'ms_jabatan';
                $id_logs     = $data->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($request->all());

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->table               = $table_logs;
                $data->primary             = $id_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;
                $data->param               = $param_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            return redirect(url('mainmenu/pos_home'))->with('status', ' Updated position has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage position delete function--------------------------------------------------
        public function delete_pos($id)
        {
            $data = manage_position::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->delete_date      = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Position';
                $table_logs  = 'ms_jabatan';
                $id_logs     = $data->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->table               = $table_logs;
                $data->primary             = $id_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            return redirect(url('mainmenu/pos_home'))->with('status', ' Deleted position has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage position range function---------------------------------------------------
        public function pos_range(Request $request)
        {
            $start = $request->start_date;
            $end   = $request->end_date;

            $date_start = strtotime($start);
            $date_start_format = date("Y-m-d",$date_start);

            $date_end = strtotime($end);
            $date_end_format = date("Y-m-d",$date_end);

            // query left join
            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->whereBetween('created_date', [$date_start_format, $date_end_format])
                        ->get();

            $arraydate = [$date_start_format,$date_end_format];

            // save logs---------------------------------------------------
                $do_logs     = 'Do Search Position';
                $table_logs  = 'ms_jabatan';
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($request->all());

                $data = new manage_logs;
                $data->do                  = $do_logs;
                $data->table               = $table_logs;
                $data->url                 = $url_logs;
                $data->ip                  = $ip_logs;
                $data->param               = $param_logs;

                $data->created_date        = date('Y-m-d H:i:s');
                $data->last_modify_date    = date('Y-m-d H:i:s');
                $data->modify_user_id      = Auth::user()->karyawan_id;
                $data->status              = 'A';
                $data->save();
            // save logs---------------------------------------------------

            // return to view
            return view('mainmenu/position/poshome', ['ms_jabatan' => $ms_jabatan, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage position-----------------------------------------------------------------

}
