<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;



use App\Models\manage_logs;

use Auth;
use Hash;
use Excel;
use PHPExcel_Worksheet_Drawing;

class LogsList extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu logs----------------------------------------------------------------------------

    	// logs index function--------------------------------------------------------------
        public function index_logs()
        {
            // query left join
            $logs = DB::table('logs')
                        ->select('logs.*', 'ms_karyawan.nama AS name')
                        ->leftJoin('ms_karyawan', 'logs.modify_user_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('logs.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Logs List';
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
            return view('admin/logs/logshome', ['logs' => $logs]);
        }
        // ---------------------------------------------------------------------------------

        // logs range function--------------------------------------------------------------
        public function logs_range(Request $request)
        {
            $start = $request->start_date;
            $end   = $request->end_date;

            $date_start = strtotime($start);
            $date_start_format = date("Y-m-d",$date_start);

            $date_end = strtotime($end);
            $date_end_format = date("Y-m-d",$date_end);

            // query left join
            $logs = DB::table('logs')
                        ->select('logs.*', 'ms_karyawan.nama AS name')
                        ->leftJoin('ms_karyawan', 'logs.modify_user_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('logs.status', 'A')
                        ->whereBetween('logs.created_date', [$date_start_format, $date_end_format])
                        ->get();

            $arraydate = [$date_start_format,$date_end_format];

            // save logs---------------------------------------------------
                $do_logs     = 'Do Search Logs List';
                $table_logs  = 'logs';
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
            return view('admin/logs/logshome', ['logs' => $logs, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

        // logs do edit function------------------------------------------------------------
        public function do_edit_logs(Request $request, $id)
        {

            $data = manage_logs::find($id);
            $data->note             = $request->note;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit Logs';
                $table_logs  = 'logs';
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

            return redirect(url('admin/logs_home'))->with('status', ' Updated note logs has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu logs----------------------------------------------------------------------------
}
