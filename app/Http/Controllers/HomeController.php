<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_items_temp;
use App\Models\manage_items;
use App\Models\manage_selling_history;
use App\Models\manage_selling;
use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\manage_debt;
use App\Models\deposit_supp;
use App\Models\manage_credit;
use App\Models\manage_service;
use App\Models\deposit_cust;

use App\Models\manage_logs;

use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // query left join

        $lt_hutang = DB::select("SELECT a.id AS 'id_debt', a.created_date AS 'createddate',
                              a.hutang_id AS 'debtid', b.pembelian_id AS 'buyid',
                              b.supplier_id AS 'suppid', a.total AS 'total', b.no_nota AS 'nota',
                              b.tanggal_jatuh_tempo_pembelian AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_hutang a LEFT JOIN ms_pembelian b
                              ON a.pembelian_id = b.id
                              WHERE a.`status` = 'A'
                              AND a.status_hutang = 1
                              AND b.tanggal_jatuh_tempo_pembelian >= DATE(NOW())
                              ORDER BY b.tanggal_jatuh_tempo_pembelian ASC
                              LIMIT 10");

        $lt_hutang_to = DB::select("SELECT a.id AS 'id_debt', a.created_date AS 'createddate',
                              a.hutang_id AS 'debtid', b.pembelian_id AS 'buyid',
                              b.supplier_id AS 'suppid', a.total AS 'total', b.no_nota AS 'nota',
                              b.tanggal_jatuh_tempo_pembelian AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_hutang a LEFT JOIN ms_pembelian b
                              ON a.pembelian_id = b.id
                              WHERE a.`status` = 'A'
                              AND a.status_hutang = 1
                              AND b.tanggal_jatuh_tempo_pembelian < DATE(NOW())
                              ORDER BY b.tanggal_jatuh_tempo_pembelian ASC
                              LIMIT 50");

        $lt_piutang = DB::select("SELECT a.id AS 'id_credit', a.created_date AS 'createddate',
                              a.piutang_id AS 'creditid', b.penjualan_id AS 'sellid',
                              b.customer_id AS 'custid', a.total AS 'total', b.no_nota AS 'nota',
                              b.tanggal_jatuh_tempo_penjualan AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_piutang a LEFT JOIN ms_penjualan b
                              ON a.penjualan_id = b.id
                              WHERE a.`status` = 'A'
                              AND a.status_piutang = 1
                              AND b.tanggal_jatuh_tempo_penjualan >= DATE(NOW())
                              ORDER BY b.tanggal_jatuh_tempo_penjualan ASC
                              LIMIT 10");

        $lt_piutang_to = DB::select("SELECT a.id AS 'id_credit', a.created_date AS 'createddate',
                              a.piutang_id AS 'creditid', b.penjualan_id AS 'sellid',
                              b.customer_id AS 'custid', a.total AS 'total', b.no_nota AS 'nota',
                              b.tanggal_jatuh_tempo_penjualan AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_piutang a LEFT JOIN ms_penjualan b
                              ON a.penjualan_id = b.id
                              WHERE a.`status` = 'A'
                              AND a.status_piutang = 1
                              AND b.tanggal_jatuh_tempo_penjualan < DATE(NOW())
                              ORDER BY b.tanggal_jatuh_tempo_penjualan ASC
                              LIMIT 50");

        $tr_deposit_supp = DB::select("SELECT b.supplier_id AS 'suppid', b.nama AS 'suppname', a.deposit AS 'total'
                    FROM tr_deposit_supplier a LEFT JOIN ms_supplier b
                    ON a.supplier_id = b.supplier_id
                    WHERE a.`status` = 'A'
                    ORDER BY a.deposit DESC
                    LIMIT 10");

        $tr_deposit_cust = DB::select("SELECT b.customer_id AS 'custid', b.nama AS 'custname', a.deposit AS 'total'
                    FROM tr_deposit_customer a LEFT JOIN ms_customer b
                    ON a.customer_id = b.customer_id
                    WHERE a.`status` = 'A'
                    ORDER BY a.deposit DESC
                    LIMIT 10");

        $lt_loan_emp = DB::select("SELECT a.id AS 'id_loan_emp', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_karyawan b
                              ON a.user_id = b.karyawan_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 3) = 'EMP'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo >= DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 10");

        $lt_loan_emp_to = DB::select("SELECT a.id AS 'id_loan_emp', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_karyawan b
                              ON a.user_id = b.karyawan_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 3) = 'EMP'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo < DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 50");

        $lt_loan_supp = DB::select("SELECT a.id AS 'id_loan_supp', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_supplier b
                              ON a.user_id = b.supplier_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 4) = 'SUPP'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo >= DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 10");

        $lt_loan_supp_to = DB::select("SELECT a.id AS 'id_loan_supp', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_supplier b
                              ON a.user_id = b.supplier_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 4) = 'SUPP'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo < DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 50");

        $lt_loan_cust = DB::select("SELECT a.id AS 'id_loan_cust', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_customer b
                              ON a.user_id = b.customer_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 4) = 'CUST'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo >= DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 10");

        $lt_loan_cust_to = DB::select("SELECT a.id AS 'id_loan_cust', a.created_date AS 'createddate',
                              a.loan_id AS 'loanid', a.user_id AS 'userid', b.nama AS 'name',
                              a.total AS 'total', a.tanggal_jatuh_tempo AS 'duedate', a.last_modify_date AS 'modifydate'
                              FROM lt_loan a LEFT JOIN ms_customer b
                              ON a.user_id = b.customer_id
                              WHERE a.`status` = 'A'
                              AND SUBSTR(a.user_id, 1, 4) = 'CUST'
                              AND a.status_loan = 1
                              AND a.tanggal_jatuh_tempo < DATE(NOW())
                              ORDER BY a.tanggal_jatuh_tempo ASC
                              LIMIT 50");

        $barang_temp = manage_items_temp::where('status', 'A')->get();

        $bulan = date('m');
        $date = date('Y-m-d');
        $gross = manage_selling::where('tanggal', '>=', $date.' 00:00:00')->where('tanggal', '<=', $date.' 23:59:59')->where('status', 'A')->sum('penjualan_total');
        $totalbuying = manage_buying::where('tanggal', '>=', $date.' 00:00:00')->where('tanggal', '<=', $date.' 23:59:59')->where('status', 'A')->sum('pembelian_total');
        $net = $gross-$totalbuying;
        $arraydata = [$date,$date];

        // return to view
        return view('template/admin', ['lt_hutang' => $lt_hutang, 'lt_hutang_to' => $lt_hutang_to,
          'lt_piutang' => $lt_piutang, 'lt_piutang_to' => $lt_piutang_to, 'barang_temp' => $barang_temp,
          'tr_deposit_supp' => $tr_deposit_supp, 'tr_deposit_cust' => $tr_deposit_cust,
          'lt_loan_emp' => $lt_loan_emp, 'lt_loan_emp_to' => $lt_loan_emp_to,
          'lt_loan_supp' => $lt_loan_supp, 'lt_loan_supp_to' => $lt_loan_supp_to,
          'lt_loan_cust' => $lt_loan_cust, 'lt_loan_cust_to' => $lt_loan_cust_to,
          'gross' => $gross, 'totalbuying' => $totalbuying, 'net' => $net, 'arraydata' => $arraydata]);
    }

    public function tempfix(Request $request)
    {
      $tempid =  $request->idbarangtemp;
      $baranggantiid =  $request->items_id;
      $type =  $request->type;

      if($baranggantiid=="addnew"){
        return redirect(url('dashboard/home'))->with('error', 'You can not choose other again');
      }

      $temp = manage_items_temp::find($tempid);
      $temp->status = 'D';
      $temp->save();

      $mspembelianid = 0;
      if($type==1){
        $barangfix = manage_service::find($baranggantiid);
      } else {
        $barangfix = manage_items::find($baranggantiid);
      }
      if($temp->harga_jual==0){
        $detailberubah = manage_buying_history::where('barang_id', $tempid)->where('status', 'T')->get();
        foreach ($detailberubah as $item) {
          $detailfix = manage_buying_history::find($item->id);
          $detailfix->barang_id = $baranggantiid;
          $detailfix->sub_total_pembelian = $barangfix->harga;
          $detailfix->status = 'A';
          $detailfix->save();

          $barangfix->stock = $barangfix->stock + $temp->qty;
          $barangfix->save();

          $mspembelianid = $detailfix->ms_pembelian_id;
        }

        //mengubah status hutang
        $mspembelian = manage_buying::find($mspembelianid);
        if($mspembelian->status_bayar_pembelian==0){

            //mendapatkan uang asli
            $totalpembelianawal = $mspembelian->pembelian_total;

            //uang setelah
            $totalpembeliansetelah = 0;
            $historypembelian = manage_buying_history::where('ms_pembelian_id', $mspembelian->id)->get();
            foreach($historypembelian as $row){
              $totalpembeliansetelah += $row->sub_total_pembelian * $row->qty;
            }

            $selisihpembelian = $totalpembeliansetelah - $totalpembelianawal;

            $hutang0 = manage_debt::where('pembelian_id', $mspembelian->id)->first();
            $hutang = manage_debt::find($hutang0->id);
            $hutang->total = $hutang->total + $selisihpembelian;
            $hutang->save();

            $deposit0 = deposit_supp::where('supplier_id', $mspembelian->supplier_id)->first();
            $deposit = deposit_supp::find($deposit0->id);
            $deposit->deposit = $deposit->deposit + $selisihpembelian;
            $deposit->save();
        }

      } else {
        $detailberubah = manage_selling_history::where('barang_id', $tempid)->where('status', 'T')->get();
        foreach ($detailberubah as $item) {
          $detailfix = manage_selling_history::find($item->id);
          $detailfix->barang_id = $baranggantiid;
          if($type==1){
            $detailfix->sub_total_penjualan = $barangfix->price;
          } else {
            $detailfix->sub_total_penjualan = $barangfix->harga_jual;
          }
          $detailfix->status = 'A';
          $detailfix->save();

          if($type==1){
            $barangfix->qty = $barangfix->qty - $temp->qty;
          } else {
            $barangfix->stock = $barangfix->stock - $temp->qty;
          }
          $barangfix->save();

          $mspembelianid = $detailfix->detail_penjualan_id;
        }

        //mengubah status hutang
        $mspenjualan = manage_selling::find($mspembelianid);
        if($mspenjualan->status_bayar_penjualan==0){

            //mendapatkan uang asli
            $totalpenjualanawal = $mspenjualan->penjualan_total;

            //uang setelah
            $totalpenjualansetelah = 0;
            $historypenjualan = manage_selling_history::where('detail_penjualan_id', $mspenjualan->id)->get();
            foreach($historypenjualan as $row){
              $totalpenjualansetelah += $row->sub_total_penjualan * $row->qty;
            }

            $selisihpenjualan = $totalpenjualansetelah - $totalpenjualanawal;

            $piutang0 = manage_credit::where('penjualan_id', $mspenjualan->id)->first();
            $piutang = manage_credit::find($piutang0->id);
            $piutang->total = $piutang->total + $selisihpenjualan;
            $piutang->save();

            $deposit0 = deposit_cust::where('customer_id', $mspenjualan->customer_id)->first();
            $deposit = deposit_cust::find($deposit0->id);
            $deposit->deposit = $deposit->deposit + $selisihpenjualan;
            $deposit->save();
        }
      }

      return redirect(url('dashboard/home'));
    }

    public function tempfixget($id)
    {
      $barangtemp = manage_items_temp::find($id);
      $type = 2;
      if($barangtemp->harga_beli==0){
        $penjualan = manage_selling_history::where('barang_id', $barangtemp->id)->where('status', 'T')->first();
        $type = $penjualan->type_sell;
      }
      $data[] = array(
            'name_itemp' => $barangtemp->nama,
            'qty_itemp' => $barangtemp->qty,
            'type' => $type
        );
        return json_encode($data);
    }


}
