<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_logs;
use App\Models\manage_karyawan;
use App\Models\manage_loan;

use Auth;
use Hash;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Developers extends Controller
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
            return view('developers/logs/logshome', ['logs' => $logs]);
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
            return view('developers/logs/logshome', ['logs' => $logs, 'arraydate' => $arraydate]);
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

            return redirect(url('developers/logs_home'))->with('status', ' Updated note logs has been success.');
        }
        // ---------------------------------------------------------------------------------

    // menu logs----------------------------------------------------------------------------

    // menu destroy------------------------------------------------------------------------
        
        public function index_destroy()
        {
          return view('developers/destroytable/home');
        }

        public function login_destroy(Request $request)
        {
          $pass = $request->pass;
          if (Hash::check($pass, Auth::user()->password))
          {
            DB::select("DELETE FROM logs");
            DB::select("DELETE FROM lt_biayalain");
            DB::select("DELETE FROM lt_biayalain_detail");
            DB::select("DELETE FROM lt_hutang");
            DB::select("DELETE FROM lt_loan");
            DB::select("DELETE FROM lt_pemakaiansolar");
            DB::select("DELETE FROM lt_piutang");
            DB::select("DELETE FROM lt_route");
            DB::select("DELETE FROM lt_solar");
            DB::select("DELETE FROM ms_barang");
            DB::select("DELETE FROM ms_barang_temp");
            DB::select("DELETE FROM ms_customer");
            DB::select("DELETE FROM ms_department");
            DB::select("DELETE FROM ms_gaji");
            DB::select("DELETE FROM ms_jabatan");
            DB::select("DELETE FROM ms_jasa");
            DB::select("DELETE FROM ms_kategori_barang");
            DB::select("DELETE FROM ms_mobil");
            DB::select("DELETE FROM ms_pelabuhan");
            DB::select("DELETE FROM ms_pembelian");
            DB::select("DELETE FROM ms_penjualan");
            DB::select("DELETE FROM ms_returpembelian");
            DB::select("DELETE FROM ms_returpenjualan");
            DB::select("DELETE FROM ms_supplier");
            DB::select("DELETE FROM ms_tambang");
            DB::select("DELETE FROM tr_credit_history");
            DB::select("DELETE FROM tr_debt_history");
            DB::select("DELETE FROM tr_deposit_customer");
            DB::select("DELETE FROM tr_deposit_supplier");
            DB::select("DELETE FROM tr_detail_pembelian");
            DB::select("DELETE FROM tr_detail_penjualan");
            DB::select("DELETE FROM tr_houling");
            DB::select("DELETE FROM tr_loan_history");
            DB::select("DELETE FROM tr_returpembelian");
            DB::select("DELETE FROM tr_returpenjualan");
            DB::select("DELETE FROM tr_tonase");

            // created all loan deposit for employee

            $ms_karyawan = DB::select("SELECT karyawan_id FROM ms_karyawan WHERE `status` = 'A' ORDER BY nama ASC");

            foreach ($ms_karyawan as $item) {
                
                // validate increment id
                    $id_loan = DB::table('lt_loan')->orderBy('id', 'desc')->first();
                    if($id_loan != null){
                            $lastnumber = substr($id_loan->loan_id,2,8);
                            $next_id    = $lastnumber + 1;
                            $id_loan    = "LN".sprintf("%08d", $next_id);
                    }else{
                            $id_loan    = "LN00000001";
                    }
                // validate increment id

                $data = new manage_loan;
                $data->loan_id                      = $id_loan;
                $data->user_id                      = $item->karyawan_id;
                $data->total                        = 0;
                $data->bayar                        = 0;
                $data->status_loan                  = 2;
                $data->loan_type                    = 1;
                $data->tanggal_jatuh_tempo          = date('Y-m-d H:i:s');
                $data->created_date                 = date('Y-m-d H:i:s');
                $data->last_modify_date             = date('Y-m-d H:i:s');
                $data->modify_user_id               = Auth::user()->karyawan_id;
                $data->status                       = 'A';
                $data->save();

            }

            return redirect(url('developers/destroy_home'))->with('status', ' Destroy Data has been success.');
          } else {
            return redirect(url('developers/destroy_home'))->with('error', ' These credentials do not match our records.');
          }
        }

        public function error()
        {
          return view('developers/404');
        }

    // menu destroy------------------------------------------------------------------------

}
