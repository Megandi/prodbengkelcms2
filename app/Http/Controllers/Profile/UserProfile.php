<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\users;


use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class UserProfile extends Controller
{
	function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
}
