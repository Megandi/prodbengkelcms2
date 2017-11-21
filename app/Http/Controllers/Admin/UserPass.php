<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\users;

use App\Models\manage_logs;

use Auth;
use Hash;
use Excel;
use PHPExcel_Worksheet_Drawing;

class UserPass extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu user pass-----------------------------------------------------------------------

        // user pass index function---------------------------------------------------------
        public function index_pass()
        {
            // query left join
            $users = DB::table('users')
                        ->select('users.*', 'ms_karyawan.nama AS name_employee', 'lt_user_type.name AS level_name', 'ms_department.name AS department_name', 'ms_jabatan.name AS position_name')
                        ->leftJoin('ms_karyawan', 'users.karyawan_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('lt_user_type', 'users.level_id', '=', 'lt_user_type.id')
                        ->leftJoin('ms_department', 'users.department_id', '=', 'ms_department.id')
                        ->leftJoin('ms_jabatan', 'users.position_id', '=', 'ms_jabatan.id')
                        ->where('users.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to User Pass';
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
            return view('admin/userpass/userpasshome', ['users' => $users]);
        }
        // ---------------------------------------------------------------------------------

        // user pass edit function----------------------------------------------------------
        public function edit_user_pass($id)
        {

            $users = DB::table('users')
                        ->select('users.*', 'ms_karyawan.nama AS name_employee', 'lt_user_type.name AS level_name', 'ms_department.name AS department_name', 'ms_jabatan.name AS position_name')
                        ->leftJoin('ms_karyawan', 'users.karyawan_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('lt_user_type', 'users.level_id', '=', 'lt_user_type.id')
                        ->leftJoin('ms_department', 'users.department_id', '=', 'ms_department.id')
                        ->leftJoin('ms_jabatan', 'users.position_id', '=', 'ms_jabatan.id')
                        ->where('users.status', 'A')
                        ->where('users.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit User Pass';
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

            return view('admin/userpass/userpassedit', ['users' => $users]);

        }
        // ---------------------------------------------------------------------------------

        // user pass do edit function-------------------------------------------------------
        public function do_edit_user_pass(Request $request, $id)
        {
            $this->validate($request, [
                'password_user'     => 'required|min:8|max:50',
                're_password_user'  => 'required|min:8|max:50'

            ]);

            $pass1 = $request->password_user;
            $pass2 = $request->re_password_user;

            if($pass1 != $pass2){
                return redirect(url('admin/user_pass_home/edit/'.$id))->with('error', ' Password not match.');
            }

            $data = users::find($id);
            $data->password         = bcrypt($request->password_user);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->remember_token   = '';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit User Pass';
                $table_logs  = 'users';
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

            return redirect(url('admin/user_pass_home'))->with('status', ' Updated user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user pass delete function--------------------------------------------------------
        public function delete_user_pass($id)
        {
            $data = users::find($id);
            $data->delete_date      = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete User Pass';
                $table_logs  = 'users';
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

            return redirect(url('admin/user_pass_home'))->with('status', ' Deleted user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user pass range function---------------------------------------------------------
        public function user_range_pass(Request $request)
        {
            $start = $request->start_date;
            $end   = $request->end_date;

            $date_start = strtotime($start);
            $date_start_format = date("Y-m-d",$date_start);

            $date_end = strtotime($end);
            $date_end_format = date("Y-m-d",$date_end);

            // query left join
            $users = DB::table('users')
                        ->select('users.*', 'ms_karyawan.nama AS name_employee', 'lt_user_type.name AS level_name', 'ms_department.name AS department_name', 'ms_jabatan.name AS position_name')
                        ->leftJoin('ms_karyawan', 'users.karyawan_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('lt_user_type', 'users.level_id', '=', 'lt_user_type.id')
                        ->leftJoin('ms_department', 'users.department_id', '=', 'ms_department.id')
                        ->leftJoin('ms_jabatan', 'users.position_id', '=', 'ms_jabatan.id')
                        ->where('users.status', 'A')
                        ->whereBetween('users.created_at', [$date_start_format, $date_end_format])
                        ->get();

            $arraydate = [$date_start_format,$date_end_format];

            // save logs---------------------------------------------------
                $do_logs     = 'Do Search User Pass';
                $table_logs  = 'users';
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
            return view('admin/userpass/userpasshome', ['users' => $users, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu user pass-----------------------------------------------------------------------
}
