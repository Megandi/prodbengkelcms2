<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\users;
use App\Models\manage_level;
use App\Models\manage_level_akses;

use App\Models\manage_logs;

use Auth;
use Hash;
use Excel;
use PHPExcel_Worksheet_Drawing;

class UserLevel extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('admin/level/levelhome', ['lt_user_type' => $lt_user_type]);
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

            return view('admin/level/levelset', ['level_akses' => $level_akses, 'id' => $id]);
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

            return view('admin/level/leveladd');
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

            return redirect(url('admin/level_home'))->with('status', ' Created new level has been success.');
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

            return view('admin/level/leveledit', ['lt_user_type' => $lt_user_type]);
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

            return redirect(url('admin/level_home'))->with('status', ' Updated level has been success.');
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

            return redirect(url('admin/level_home'))->with('status', ' Deleted level has been success.');
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

            return redirect(url('admin/level_home'))->with('status', ' Updated new Level Access has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu user level----------------------------------------------------------------------
}
