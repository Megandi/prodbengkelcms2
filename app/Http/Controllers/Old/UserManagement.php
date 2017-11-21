<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\users;
use App\Models\manage_department;
use App\Models\manage_position;
use App\Models\manage_logs;
use App\Models\manage_level;
use App\Models\manage_level_akses;

use Auth;
use Excel;

class UserManagement extends Controller
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
            return view('user_management/userhome', ['users' => $users]);
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

            return view('user_management/useradd', ['lt_user_type' => $lt_user_type, 'ms_department' => $ms_department, 'ms_jabatan' => $ms_jabatan]);

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
                return redirect(url('usermanagement/user_home/add'))->with('error', ' Password not match.');
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

            return redirect(url('usermanagement/user_home'))->with('status', ' Created new user has been success.');
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

            return view('user_management/useredit', ['lt_user_type' => $lt_user_type, 'ms_department' => $ms_department, 'ms_jabatan' => $ms_jabatan, 'users' => $users]);
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
                return redirect(url('usermanagement/user_home/edit/'.$id))->with('error', ' Password not match.');
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

            return redirect(url('usermanagement/user_home'))->with('status', ' Updated user has been success.');
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

            return redirect(url('usermanagement/user_home'))->with('status', ' Deleted user has been success.');
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
            return view('user_management/userhome', ['users' => $users, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu user list-----------------------------------------------------------------------

    // menu manage department---------------------------------------------------------------

        // manage department index function-------------------------------------------------
        public function index_dep()
        {
            // query left join
            $ms_department = DB::table('ms_department')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Department';
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
            return view('user_management/department/dephome', ['ms_department' => $ms_department]);
        }
        // ---------------------------------------------------------------------------------

        // manage department add function---------------------------------------------------
        public function add_dep()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Department';
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

            return view('user_management/department/depadd');
        }
        // ---------------------------------------------------------------------------------

        // manage department do add function------------------------------------------------
        public function do_add_dep(Request $request)
        {
            $this->validate($request, [
                'name'       => 'required|max:100|unique:ms_department'
            ]);

            $data = new manage_department;
            $data->name             = $request->name;
            $data->created_date     = date('Y-m-d H:i:s');
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Department'; 
                $table_logs  = 'ms_department';
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

            return redirect(url('usermanagement/dep_home'))->with('status', ' Created new department has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage department edit function--------------------------------------------------
        public function edit_dep($id)
        {
            $ms_department = DB::table('ms_department')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Department';
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

            return view('user_management/department/depedit', ['ms_department' => $ms_department]);
        }
        // ---------------------------------------------------------------------------------

        // manage department do edit function-----------------------------------------------
        public function do_edit_dep(Request $request, $id)
        {
            $this->validate($request, [
                'name'       => 'required|max:100'
            ]);

            $data = manage_department::find($id);
            $data->name             = $request->name;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Department'; 
                $table_logs  = 'ms_department';
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

            return redirect(url('usermanagement/dep_home'))->with('status', ' Updated depatment has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage department delete function------------------------------------------------
        public function delete_dep($id)
        {
            $data = manage_department::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->delete_date      = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Department'; 
                $table_logs  = 'ms_department';
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


            return redirect(url('usermanagement/dep_home'))->with('status', ' Deleted department has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage department range function-------------------------------------------------
        public function dep_range(Request $request)
        {
            $start = $request->start_date; 
            $end   = $request->end_date;

            $date_start = strtotime($start);
            $date_start_format = date("Y-m-d",$date_start);

            $date_end = strtotime($end);
            $date_end_format = date("Y-m-d",$date_end);

            // query left join
            $ms_department = DB::table('ms_department')
                        ->where('status', 'A')
                        ->whereBetween('created_date', [$date_start_format, $date_end_format])
                        ->get();

            $arraydate = [$date_start_format,$date_end_format];

            // save logs---------------------------------------------------
                $do_logs     = 'Do Search Department'; 
                $table_logs  = 'ms_department';
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
            return view('user_management/department/dephome', ['ms_department' => $ms_department, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage department---------------------------------------------------------------

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
            return view('user_management/position/poshome', ['ms_jabatan' => $ms_jabatan]);
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

            return view('user_management/position/posadd');
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

            return redirect(url('usermanagement/pos_home'))->with('status', ' Created new position has been success.');
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

            return view('user_management/position/posedit', ['ms_jabatan' => $ms_jabatan]);
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

            return redirect(url('usermanagement/pos_home'))->with('status', ' Updated position has been success.');
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

            return redirect(url('usermanagement/pos_home'))->with('status', ' Deleted position has been success.');
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
            return view('user_management/position/poshome', ['ms_jabatan' => $ms_jabatan, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage position-----------------------------------------------------------------

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

            return view('user_management/userprofile');
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
                return redirect(url('usermanagement/user_profile/'.$id))->with('error', ' Password not match.');
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

            return redirect(url('usermanagement/user_profile/'.$id))->with('status', ' Updated position has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu manage profile------------------------------------------------------------------

    // menu user level----------------------------------------------------------------------

        // user level index function--------------------------------------------------------
        public function index_level()
        {
            // query left join
            $lt_user_type = DB::table('lt_user_type')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Level';
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
            return view('user_management/level/levelhome', ['lt_user_type' => $lt_user_type]);
        }
        // ---------------------------------------------------------------------------------

        // user level edit set level function-----------------------------------------------
        public function edit_set_level($id)
        {
            $level_akses = DB::table('menus')
                        ->select('level_akses.*','menus.nama AS nama','menus.id AS id_menus')
                        ->leftJoin('level_akses', 'menus.id', '=', 'level_akses.id_menu')
                        ->where('level_akses.id_level', $id)
                        ->get();

                        if (sizeof($level_akses) == 0) {
                                $level_akses = DB::table('menus')
                                ->select('menus.nama AS nama',DB::Raw("'0' as 'c','0' as 'r','0' as 'u','0' as 'd'"),'menus.id AS id_menus')
                                ->get();
                        }

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Set Level';
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

            return view('user_management/level/levelset', ['level_akses' => $level_akses, 'id' => $id]);
        }
        // ---------------------------------------------------------------------------------

        // user level add function----------------------------------------------------------
        public function add_level()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Level';
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

            return view('user_management/level/leveladd');
        }
        // ---------------------------------------------------------------------------------

        // user level do add function-------------------------------------------------------
        public function do_add_level(Request $request)
        {
            $this->validate($request, [
                'name'       => 'required|max:50|unique:lt_user_type'
            ]);

            $data = new manage_level;
            $data->name             = $request->name;
            $data->created_date     = date('Y-m-d H:i:s');
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            $lt_user_type = DB::table('lt_user_type')
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->first();

            $menus = DB::table('menus')
                    ->get();

            foreach ($menus as $item) {

                $id_level = $lt_user_type->id;

                $data = new manage_level_akses;
                $data->id_level         = $id_level;
                $data->id_menu          = $item->id;
                $data->created_date     = date('Y-m-d H:i:s');
                $data->last_modify_date = date('Y-m-d H:i:s');
                $data->modify_user_id   = Auth::user()->karyawan_id;
                $data->status           = 'A';
                $data->save();

            }

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New level'; 
                $table_logs  = 'lt_user_type';
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

            return redirect(url('usermanagement/level_home'))->with('status', ' Created new level has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user level edit function---------------------------------------------------------
        public function edit_level($id)
        {
            $lt_user_type = DB::table('lt_user_type')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Level';
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

            return view('user_management/level/leveledit', ['lt_user_type' => $lt_user_type]);
        }
        // ---------------------------------------------------------------------------------

        // user level do edit function------------------------------------------------------
        public function do_edit_level(Request $request, $id)
        {
            $this->validate($request, [
                'name'       => 'required|max:100'
            ]);

            $data = manage_level::find($id);
            $data->name             = $request->name;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Level'; 
                $table_logs  = 'lt_user_type';
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

            return redirect(url('usermanagement/level_home'))->with('status', ' Updated level has been success.');
        }
        // ---------------------------------------------------------------------------------

        // user level delete function-------------------------------------------------------
        public function delete_level($id)
        {
            $data = manage_level::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->deleted_date      = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            $data = manage_level_akses::where('id_level', '=', $id)->update(array('status' => 'D', 'modify_user_id' => Auth::user()->karyawan_id, 'deleted_date' => date('Y-m-d H:i:s') ));

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Level'; 
                $table_logs  = 'lt_user_type';
                $id_logs     = $id;
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

            return redirect(url('usermanagement/level_home'))->with('status', ' Deleted level has been success.');
        }
        // ---------------------------------------------------------------------------------

        // level do add set menu function
        public function do_add_set_menu(Request $request)
        {
            $id = $request->id_level;

            $level_akses = DB::table('menus')
                ->select('level_akses.*','menus.nama AS nama','menus.id AS id_menus')
                ->leftJoin('level_akses', 'menus.id', '=', 'level_akses.id_menu')
                ->where('level_akses.id_level', $id)
                ->get();

                if (sizeof($level_akses) == 0) {
                        $level_akses = DB::table('menus')
                        ->select('menus.nama AS nama',DB::Raw("'0' as 'c','0' as 'r','0' as 'u','0' as 'd','0' as 'e'"),'menus.id AS id_menus')
                        ->get();
                }

                manage_level_akses::where('id_level',$id)->delete();

                foreach ($level_akses as $item) {
                    $data = new manage_level_akses;
                    $data->id_level         = $id;
                    $data->id_menu          = $item->id_menus;
                    $data->c                = $request->has('create_'.$item->id_menus)?1:0;
                    $data->r                = $request->has('read_'.$item->id_menus)?1:0;
                    $data->u                = $request->has('update_'.$item->id_menus)?1:0;
                    $data->d                = $request->has('delete_'.$item->id_menus)?1:0;
                    $data->e                = $request->has('export_'.$item->id_menus)?1:0;
                    $data->modify_user_id   = Auth::user()->karyawan_id;
                    $data->created_date     = date("Y-m-d H:i:s");
                    $data->last_modify_date = date("Y-m-d H:i:s");
                    $data->status           = 'A';
                    $data->save();  
                }

                $data = manage_level::find($id);
                $data->modify_user_id   = Auth::user()->karyawan_id;
                $data->last_modify_date = date('Y-m-d H:i:s');
                $data->status           = 'A';
                $data->save();

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Edit New Set Level'; 
                    $table_logs  = 'level_akses';
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

            return redirect(url('usermanagement/level_home'))->with('status', ' Updated new Level Access has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu user level----------------------------------------------------------------------

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
            return view('user_management/userpass/userpasshome', ['users' => $users]);
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

            return view('user_management/userpass/userpassedit', ['users' => $users]);

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
                return redirect(url('usermanagement/user_pass_home/edit/'.$id))->with('error', ' Password not match.');
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

            return redirect(url('usermanagement/user_pass_home'))->with('status', ' Updated user has been success.');
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

            return redirect(url('usermanagement/user_pass_home'))->with('status', ' Deleted user has been success.');
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
            return view('user_management/userpass/userpasshome', ['users' => $users, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu user pass-----------------------------------------------------------------------
}
