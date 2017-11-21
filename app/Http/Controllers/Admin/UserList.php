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

class UserList extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu user list-----------------------------------------------------------------------

    	// user list index function---------------------------------------------------------
        public function index()
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
                $do_logs     = 'Go to User List';
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
            return view('admin/userhome', ['users' => $users]);
        }
        // ---------------------------------------------------------------------------------

        // user list select2 employee function----------------------------------------------
        public function search_employee()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_karyawan = DB::table('ms_karyawan')
                            ->where('status',"A")
                            ->where('nama','like', "%".$term."%" )
                            ->orWhere('karyawan_id','like', "%".$term."%" )
                            ->get();

            $query = $ms_karyawan;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->karyawan_id));
                    $new_row['text']=htmlentities(stripslashes($row->karyawan_id." - ".$row->nama));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // user list add function-----------------------------------------------------------
        public function add_user()
        {
            $lt_user_type = DB::table('lt_user_type')
                        ->where('status', 'A')
                        ->get();

            $ms_department = DB::table('ms_department')
                        ->where('status', 'A')
                        ->get();

            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

            return view('admin/useradd', ['lt_user_type' => $lt_user_type, 'ms_department' => $ms_department, 'ms_jabatan' => $ms_jabatan]);

            // save logs---------------------------------------------------
                $do_logs     = 'Open Add User List';
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
        }
        // ---------------------------------------------------------------------------------

        // user list do add function--------------------------------------------------------
        public function do_add_user(Request $request)
        {
            $this->validate($request, [
                'karyawan_id'       => 'required|max:20',
                'email'             => 'required|max:100|email_valid|unique:users',
                'level_id'          => 'required|min:1|max:20',
                'department_id'     => 'required|min:1|max:20',
                'position_id'       => 'required|min:1|max:20',
                'password_user'     => 'required|min:8|max:50',
                're_password_user'  => 'required|min:8|max:50'

            ]);

            $pass1 = $request->password_user;
            $pass2 = $request->re_password_user;

            if($pass1 != $pass2){
                return redirect(url('admin/user_home/add'))->with('error', ' Password not match.');
            }

            $data = new users;
            $data->karyawan_id      = $request->karyawan_id;
            $data->email            = $request->email;
            $data->level_id         = $request->level_id;
            $data->department_id    = $request->department_id;
            $data->position_id      = $request->position_id;
            $data->photo            = 0;
            $data->password         = bcrypt($request->password_user);
            $data->created_at       = date('Y-m-d H:i:s');
            $data->updated_at       = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->remember_token   = '';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New User';
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

            return redirect(url('admin/user_home'))->with('status', ' Created new user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user list edit function----------------------------------------------------------
        public function edit_user($id)
        {
            $lt_user_type = DB::table('lt_user_type')
                        ->where('status', 'A')
                        ->get();

            $ms_department = DB::table('ms_department')
                        ->where('status', 'A')
                        ->get();

            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

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
                $do_logs     = 'Open Edit User List';
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

            return view('admin/useredit', ['lt_user_type' => $lt_user_type, 'ms_department' => $ms_department, 'ms_jabatan' => $ms_jabatan, 'users' => $users]);
        }
        // ---------------------------------------------------------------------------------

        // user list do edit function-------------------------------------------------------
        public function do_edit_user(Request $request, $id)
        {
            $this->validate($request, [
                'karyawan_id'       => 'required|max:20',
                'email'             => 'required|max:100|email_valid',
                'level_id'          => 'required|min:1|max:20',
                'department_id'     => 'required|min:1|max:20',
                'position_id'       => 'required|min:1|max:20',
                'password_user'     => 'required|min:8|max:50',
                're_password_user'  => 'required|min:8|max:50'

            ]);

            $pass1 = $request->password_user;
            $pass2 = $request->re_password_user;

            if($pass1 != $pass2){
                return redirect(url('admin/user_home/edit/'.$id))->with('error', ' Password not match.');
            }

            $data = users::find($id);
            $data->karyawan_id      = $request->karyawan_id;
            $data->email            = $request->email;
            $data->department_id    = $request->department_id;
            $data->position_id      = $request->position_id;
            $data->level_id         = $request->level_id;
            $data->photo            = 0;
            $data->password         = bcrypt($request->password_user);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->remember_token   = '';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New User';
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

            return redirect(url('admin/user_home'))->with('status', ' Updated user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user list delete function--------------------------------------------------------
        public function delete_user($id)
        {
            $data = users::find($id);
            $data->delete_date      = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Users';
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

            return redirect(url('admin/user_home'))->with('status', ' Deleted user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user list range function---------------------------------------------------------
        public function user_range(Request $request)
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
                $do_logs     = 'Do Search Users';
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
            return view('admin/userhome', ['users' => $users, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu user list-----------------------------------------------------------------------

    // menu manage profile------------------------------------------------------------------

        // manage profile index edit function-----------------------------------------------
        public function profile()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Go to User Profile';
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

            return view('admin/userprofile');
        }
        // ---------------------------------------------------------------------------------

        // manage profile do edit function--------------------------------------------------
        public function do_edit_profile(Request $request, $id)
        {
            $this->validate($request, [
                'password_user'       => 'required|max:20',
                're_password_user'    => 'required|max:20'
            ]);

            $pass1 = $request->password_user;
            $pass2 = $request->re_password_user;

            if($pass1 != $pass2){
                return redirect(url('admin/user_profile/'.$id))->with('error', ' Password not match.');
            }

            $data = users::find($id);
            $data->password         = bcrypt($request->password_user);
            $data->updated_at       = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New User Profile';
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

            return redirect(url('admin/user_profile/'.$id))->with('status', ' Updated position has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu manage profile------------------------------------------------------------------
}
