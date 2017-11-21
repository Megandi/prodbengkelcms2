<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_karyawan;
use App\Models\manage_loan;

use App\Models\manage_logs;

use Auth;
use Hash;
use Excel;
use PHPExcel_Worksheet_Drawing;

class DestroyData extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu destroy------------------------------------------------------------------------
        
        public function index_destroy()
        {
          return view('admin/destroytable/home');
        }

        public function login_destroy(Request $request)
        {
          $pass = $request->pass;
          if (Hash::check($pass, Auth::user()->password))
          {
            // DB::select("DELETE FROM logs");
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

            return redirect(url('admin/destroy_home'))->with('status', ' Destroy Data has been success.');
          } else {
            return redirect(url('admin/destroy_home'))->with('error', ' These credentials do not match our records.');
          }
        }

        public function error()
        {
          return view('admin/404');
        }

    // menu destroy------------------------------------------------------------------------
}
