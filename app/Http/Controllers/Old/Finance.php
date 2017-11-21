<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\manage_biayalain;
use App\Models\manage_biayalain_detail;
use App\Models\manage_debt;
use App\Models\manage_debt_history;
use App\Models\manage_purchase_return;
use App\Models\manage_other_payment;
use App\Models\manage_selling;
use App\Models\manage_selling_history;
use App\Models\manage_sales_return;
use App\Models\manage_credit;
use App\Models\manage_credit_history;
use App\Models\manage_returnpembelian;
use App\Models\manage_returnpenjualan;
use App\Models\manage_service;
use App\Models\manage_loan;
use App\Models\manage_loan_history;

use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\deposit_cust;
use App\Models\deposit_supp;
use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

// exclude load model
use App\Models\manage_supplier;
use App\Models\manage_customer;

class Finance extends Controller
{

	function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage buying-------------------------------------------------------------------

        // manage buying index function-----------------------------------------------------
        public function index_buying()
        {
			$buyings = DB::select("SELECT a.*, b.status_hutang AS 'status_hutang', c.supplier_id AS 'suppid', c.nama AS 'namasupplier'
											FROM ms_pembelian a LEFT JOIN lt_hutang b
											ON a.id = b.pembelian_id
											LEFT JOIN ms_supplier c
											ON a.supplier_id = c.supplier_id
											WHERE a.status = 'A'");

            return view('finance/buying/buyinghome', ['buyings' => $buyings]);
        }
        // ---------------------------------------------------------------------------------

        // manage buying add function-------------------------------------------------------
        public function add_buying()
        {
    		$validasi = 0;
            return view('finance/buying/buyingadd')->with('validasi', $validasi);
        }
        // ---------------------------------------------------------------------------------

    	// manage buying add next function--------------------------------------------------
        public function addnext_buying($id)
        {
    		$buying = manage_buying::find($id);
    		$validasi = 1;

    		//menghitung total------------------------------------------------------------------------
    		$total = 0;
    		$details = manage_buying_history::where('ms_pembelian_id', $id)->get();
    		foreach ($details as $detail) {
    			$total += $detail->sub_total_pembelian * $detail->qty;
    		}
    		//menghitung total------------------------------------------------------------------------

            return view('finance/buying/buying_addnext')->with('buying', $buying)
    																							->with('validasi', $validasi)
    																							->with('total', $total);
        }
        // ---------------------------------------------------------------------------------

        // manage buying select2 items function---------------------------------------------
        public function search_items_buying()
        {

            $row_set = [];
            $term = strip_tags(trim($_GET['q']));

            $ms_barang = DB::table('ms_barang')
                            ->where('status',"A")
                            ->where('barang_id','like', "%".$term."%" )
                            ->orWhere('nama','like', "%".$term."%" )
                            ->orWhere('created_date','like', "%".$term."%" )
                            ->get();

            $query = $ms_barang;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->id));
                    $new_row['text']=htmlentities(stripslashes($row->barang_id ." - ". $row->nama ." - ". date('d/m/Y', strtotime($row->created_date))));
                    $new_row['qty']=htmlentities(stripslashes($row->stock));
                    $new_row['sub_total']=htmlentities(stripslashes($row->harga));
                    $new_row['sell_price']=htmlentities(stripslashes($row->harga_jual));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
            }


            //tambah add new untuk tambah baru
            $new_row['id']="addnew";
            $new_row['text']="Other";
            $new_row['qty']="0";
            $new_row['sub_total']="0";
            $new_row['id_get_items']="0";
            $new_row['id_get_stock']="0";
            $row_set[] = $new_row; //build an array

            return json_encode($row_set); //format the array into json data
        }
        // ---------------------------------------------------------------------------------

        // manage buying select2 supplier function------------------------------------------
        public function search_supp_buying()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_supplier = DB::table('ms_supplier')
                        ->select('ms_supplier.*','tr_deposit_supplier.deposit AS deposit')
                        ->leftJoin('tr_deposit_supplier', 'ms_supplier.supplier_id', '=', 'tr_deposit_supplier.supplier_id')
                        ->where('ms_supplier.status',"A")
                        ->where('ms_supplier.nama','like', "%".$term."%" )
                        ->orWhere('ms_supplier.supplier_id','like', "%".$term."%" )
                        ->get();

            $query = $ms_supplier;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->supplier_id));
                    $new_row['text']=htmlentities(stripslashes($row->supplier_id ." - ". $row->nama));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $new_row['deposit']=htmlentities(stripslashes($row->deposit));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage buying do add function----------------------------------------------------
        public function do_add_buying(Request $request)
        {
            // validasi
            $this->validate($request, [
                'items_id'              => 'required|min:1',
                'items_qty_view'        => 'required|max:20',
                'items_sub_total_view'  => 'required|max:20'
            ]);
            // validasi

    				//buying id validasi----------------------------------------------------------------------
    				if($request->validasi==0){
    					//$msid = $data->id;
    				} else {
    					$msid = $request->id_pembelian;
    				}
    				//buying id validasi----------------------------------------------------------------------

            // calculate total pembelian-------------------------------------------------------------
            $total_price = $request->items_qty_view * $request->items_sub_total_view;
            // calculate total pembelian-------------------------------------------------------------

            // validasi quantities-------------------------------------------------------------------
            if($total_price == 0){
    					if($request->validasi==0){
                return redirect(url('finance/buying_home/add'))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    					} else {
    						return redirect(url('finance/buying_home/addnext/'.$msid))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    					}
    				}
            // validasi quantities-------------------------------------------------------------------

    				if($request->validasi==0){
    					//buat id
    					$buying = manage_buying::orderBy('id', 'DESC')->first();
    					if($buying){
    						$lastnumber = substr($buying->pembelian_id, 1, 9);
    						$idnumber = $lastnumber + 1;

    						$buyingid = "B".sprintf("%09d", $idnumber);
    					} else {
    						$buyingid = "B000000001";
    					}

    					//buat id
    	        // save to ms_pembelian------------------------------------------------------------------
    	        $data = new manage_buying;
    	        $data->pembelian_id                     = $buyingid;
    	        $data->tanggal                          = date('Y-m-d H:i:s');
    	        $data->created_date                     = date('Y-m-d H:i:s');
    	        $data->last_modify_date                 = date('Y-m-d H:i:s');
    	        $data->modify_user_id                   = Auth::user()->karyawan_id;
    					$data->status                           = 'A';
    	        $data->save();
    				}
            // save to ms_pembelian-------------------------------------------------------------------

    				//buying id validasi----------------------------------------------------------------------
    				if($request->validasi==0){
    					$msid = $data->id;
    				} else {
    					$msid = $request->id_pembelian;
    				}
    				//buying id validasi----------------------------------------------------------------------

            // save to tr_detail_pembelian------------------------------------------------------------
    				if($request->items_id=="addnew"){
    					$datatemp = new manage_items_temp;
    					$datatemp->nama = $request->new_item;
    					$datatemp->status = 'T';
    					$datatemp->qty = $request->items_qty_view;
    					$datatemp->harga_jual = $request->items_sellprice;
    					$datatemp->harga_beli = $request->items_sub_total_view;
    					$datatemp->created_date = date('Y-m-d H:i:s');
    					$datatemp->save();

    					$datadetail = new manage_buying_history;
    	        $datadetail->ms_pembelian_id             	 		= $msid;
    	        $datadetail->barang_id                        = $datatemp->id;
    	        $datadetail->qty                              = $request->items_qty_view;
    	        $datadetail->sub_total_pembelian              = $request->items_sub_total_view;
    	        $datadetail->created_date                     = date('Y-m-d H:i:s');
    	        $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
    	        $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
    	        $datadetail->status                           = 'T';
    	        $datadetail->save();

    				} else {
    					$datadetail = new manage_buying_history;
    	        $datadetail->ms_pembelian_id             	 		= $msid;
    	        $datadetail->barang_id                        = $request->items_id;
    	        $datadetail->qty                              = $request->items_qty_view;
    	        $datadetail->sub_total_pembelian              = $request->items_sub_total_view;
    	        $datadetail->created_date                     = date('Y-m-d H:i:s');
    	        $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
    	        $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
    	        $datadetail->status                           = 'A';
    	        $datadetail->save();
    		}
            // save to tr_detail_pembelian------------------------------------------------------------

            return redirect(url('finance/buying_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage buying edit function------------------------------------------------------
        public function edit_buying($id)
        {
			$buying = manage_buying::find($id);

			//menghitung total------------------------------------------------------------------------
			$total = 0;
			$details = manage_buying_history::where('ms_pembelian_id', $id)->get();
			foreach ($details as $detail) {
				$total += $detail->sub_total_pembelian * $detail->qty;
			}
			//menghitung total------------------------------------------------------------------------

            return view('finance/buying/buyingedit', ['buying' => $buying, 'total' => $total]);
        }
        // ---------------------------------------------------------------------------------

        // manage buying do edit function---------------------------------------------------
        public function do_edit_buying(Request $request, $id)
        {
            $this->validate($request, [
                'no_nota'          => 'required|max:100'
            ]);

            $data = manage_buying::find($id);
            $data->no_nota                          = $request->no_nota;
            $data->last_modify_date                 = date('Y-m-d H:i:s');
            $data->modify_user_id                   = Auth::user()->karyawan_id;
            $data->status                           = 'A';
            $data->save();

            return redirect(url('finance/buying_home'))->with('status', ' Updated buying has been success.');
        }
        // ---------------------------------------------------------------------------------

    	// manage buying delete buying detail function--------------------------------------
    	public function delete_buying_detail($detailid,$buyingid)
    	{
    		$detail = manage_buying_history::find($detailid);
    		manage_items_temp::destroy($detail->barang_id);
    		manage_buying_history::destroy($detailid);
    		return redirect(url('finance/buying_home/addnext/'.$buyingid))->with('status', ' Deleted buying has been success.');
    	}
    	// ---------------------------------------------------------------------------------

		// manage buying checkout function--------------------------------------------------
		public function index_buying_checkout($id)
		{
			$buying = manage_buying::find($id);

			//menghitung total------------------------------------------------------------------------
			$total = 0;
			$details = manage_buying_history::where('ms_pembelian_id', $id)->get();
			foreach ($details as $detail) {
				$total += $detail->sub_total_pembelian * $detail->qty;
			}
			//menghitung total------------------------------------------------------------------------

			return view('finance/buying/checkout')->with('total', $total)->with('buying', $buying);
		}
		// ---------------------------------------------------------------------------------

		// manage buying do checkout function-----------------------------------------------
		public function index_buying_docheckout(Request $request)
		{
            if($request->status_payment != 1){
                $this->validate($request, [
                    'supp_id'           => 'required|min:1',
                    'status_payment'    => 'required|min:1',
                    'sell_dp'           => 'required|max:20',
                    'sell_due_date'     => 'required'
                ]);
            }else{
                $this->validate($request, [
                    'supp_id'           => 'required|min:1',
                    'status_payment'    => 'required|min:1'
                ]);
            }

			$buying = manage_buying::find($request->buyingid);

			//menghitung total------------------------------------------------------------------------
			$total_price = 0;
			$details = manage_buying_history::where('ms_pembelian_id', $request->buyingid)->get();
			foreach ($details as $detail) {
				$total_price += $detail->sub_total_pembelian * $detail->qty;
				// // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
				if($detail->status!="T"){
					$item = manage_items::find($detail->barang_id);
					if($item->harga!=$detail->sub_total_pembelian){
						if($item->is_available==0){
							$item->stock = $detail->qty;
							$item->harga = $detail->sub_total_pembelian;
							$item->is_available = 1;
							$item->last_modify_date = date('Y-m-d H:i:s');
							$item->modify_user_id = Auth::user()->karyawan_id;
							$item->status = 'A';
							$item->save();
						} else {
							$itemadd = new manage_items;
							$itemadd->barang_id = $item->barang_id;
							$itemadd->nama = $item->nama;
							$itemadd->kategori = $item->kategori;
							$itemadd->spesifikasi = $item->spesifikasi;
							$itemadd->harga = $detail->sub_total_pembelian;
							$itemadd->harga_jual = $item->harga_jual;
							$itemadd->stock = $detail->qty;
							$itemadd->is_available = $item->is_available;
							$itemadd->created_date = date('Y-m-d H:i:s');
							$itemadd->last_modify_date = date('Y-m-d H:i:s');
							$itemadd->modify_user_id = Auth::user()->karyawan_id;
							$itemadd->status = "A";
							$itemadd->save();
						}
					} else {
						if($item->is_available==0){
							$item->stock = $detail->qty;
						} else {
							$item->stock = $item->stock + $detail->qty;
						}
						$item->is_available = 1;
						$item->last_modify_date = date('Y-m-d H:i:s');
						$item->modify_user_id = Auth::user()->karyawan_id;
						$item->status = 'A';
						$item->save();
					}
				} else if($detail->status=="T"){
						$datatemp = manage_items_temp::find($detail->barang_id);
						$datatemp->status = 'A';
						$datatemp->save();
				}
				// update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
			}
			//menghitung total------------------------------------------------------------------------

			// // validasi saat unpaid dan paid---------------------------------------------------------
			if($request->status_payment != '1'){
			    if($request->sell_dp > $total_price){
			        return redirect(url('finance/buying_home/checkout'))->with('error', ' Down payment must less than Grand Total');
			    }

			    if($request->sell_dp == $total_price){
			        return redirect(url('finance/buying_home/checkout'))->with('error', ' Down payment must less than Grand Total');
			    }
			}
			// validasi saat unpaid dan paid---------------------------------------------------------

			// Edit to ms_pembelian------------------------------------------------------------------
				$data = manage_buying::find($request->buyingid);
                if($request->no_nota != ''){
				    $data->no_nota                          = $request->no_nota;
                }else{
                    $data->no_nota                          = 'NOTAUNSET_'.$request->buyingid;
                }
				$data->supplier_id                      = $request->supp_id;
				$data->status_bayar_pembelian           = $request->status_payment;
				$data->tanggal                     			= date('Y-m-d H:i:s');
				$data->status_transaksi                 = 1;
				if($request->status_payment != '0'){
				    $data->pembelian_bayar                  = $total_price;
				    $data->tanggal_jatuh_tempo_pembelian    = date('Y-m-d H:i:s');
				    $data->pembelian_dp                     = 0;
				}else{
				    $data->pembelian_bayar                  = $request->sell_dp;
				    $data->tanggal_jatuh_tempo_pembelian    = $request->sell_due_date;
				    $data->pembelian_dp                     = $request->sell_dp;
				}
				$data->pembelian_total                  = $total_price;
				$data->save();
			// Edit to ms_pembelian-------------------------------------------------------------------

			//cek manage isinya//
			$debttt = manage_debt::orderBy('id', 'DESC')->first();
			if($debttt){
				$lastnumber = substr($debttt->hutang_id, 3, 7);
				$idnumber = $lastnumber + 1;

				$debtttid = "DEB".sprintf("%07d", $idnumber);
			} else {
				$debtttid = "DEB0000001";
			}
			//cek manage isinya//

			// // save ke lt_hutang-----------------------------------------------------------------------
			$data = new manage_debt;
			$data->hutang_id                        = $debtttid;
			$data->pembelian_id                     = $request->buyingid;

			// jika pembelian langsung lunas
			if($request->status_payment > 0){
			    $data->total                        = 0;
			    $data->bayar                        = 0;
			    $data->status_hutang                = 2;
			// jika pembelian tidak langsung lunas
			}else if($request->status_payment < 1){
			    $data->total                        = $total_price;
			    $data->bayar                        = $request->sell_dp;
			    $data->status_hutang                = 1;
			}

			$data->created_date                     = date('Y-m-d H:i:s');
			$data->last_modify_date                 = date('Y-m-d H:i:s');
			$data->modify_user_id                   = Auth::user()->karyawan_id;
			$data->status                           = 'A';
			$data->save();
			// save ke lt_hutang-----------------------------------------------------------------------

			// update total hutang ke tr_deposit_supplier ---------------------------------------------
			if($request->status_payment < 1){
			    deposit_supp::where('supplier_id',$request->supp_id)->update([
			        'deposit' => ($total_price -$request->sell_dp) + $request->supp_deposit,
			        'last_modify_date' => date('Y-m-d H:i:s'),
			        'modify_user_id' => Auth::user()->karyawan_id,
			        'status' => 'A'
			    ]);
			}
			// update total hutang ke tr_deposit_supplier ---------------------------------------------

			if($request->status_payment < 1){
				$debthistory = new manage_debt_history;
				$debthistory->hutang_id 				= $data->id;
				$debthistory->total_hutang	 		    = $total_price;
				$debthistory->total_pembayaran_hutang 	= $request->sell_dp;;
				$debthistory->created_date              = date('Y-m-d H:i:s');
				$debthistory->last_modify_date          = date('Y-m-d H:i:s');
				$debthistory->modify_user_id            = Auth::user()->karyawan_id;
				$debthistory->status                  	= 'A';
				$debthistory->save();
			}

			return redirect(url('finance/buying_home'));
		}
		// ---------------------------------------------------------------------------------

        // manage buying delete function----------------------------------------------------
        public function delete_buying($id)
        {
            $data = manage_buying::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            $datadetail = manage_buying_history::where('ms_pembelian_id',$id)->get();
    				foreach ($datadetail as $row) {
    					$datadetaill = manage_buying_history::find($row->id);
    					$datadetaill->deleted_date     = date('Y-m-d H:i:s');
    	        $datadetaill->modify_user_id   = Auth::user()->karyawan_id;
    	        $datadetaill->status           = 'D';
    	        $datadetaill->save();
    				}

            $debtt = manage_debt::where('pembelian_id',$data->id)->first();
						if($debtt){
							$debt = manage_debt::find($debtt->id);
	            $debt->deleted_date     = date('Y-m-d H:i:s');
	            $debt->modify_user_id   = Auth::user()->karyawan_id;
	            $debt->status           = 'D';
	            $debt->save();
						}
            return redirect(url('finance/buying_home'))->with('status', ' Deleted buying has been success.');
        }
        /// --------------------------------------------------------------------------------

        // manage buying range function-----------------------------------------------------
        public function buying_range(Request $request)
        {
    		//get request
    		$start		= $request->dateStart;
    		$end   		= $request->dateEnd;
    		$id				= $request->idadd;
    		$nota			= $request->nota;
    		$supp_id	= $request->supp_id;

    		// validate empty
    		if($start == "" && $end == "" && $id == "" &&  $nota == "" &&  $supp_id == ""){
    				return redirect(url('finance/buying_home'));
    		}else{

    			// validate return date if null
    			if($start != ""){
    					//convert date
    					$date_start = strtotime($start);
    					$date_start_format = date("Y-m-d",$date_start);
    					$date_end = strtotime($end);
    					$date_end_format = date("Y-m-d",$date_end);
    			}else{
    					$date_start_format = $start;
    					$date_end_format = $end;
    			}

    			$date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
    			$nota_sc 		= $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
    			$id_sc			= $id != "" ? "AND a.pembelian_id LIKE '%".$id."%'" : "";
    			$supp_id_sc = $supp_id != "" ? "AND a.supplier_id LIKE '%".$supp_id."%'" : "";

                        $buyings = DB::select("SELECT a.*, b.status_hutang AS 'status_hutang', c.supplier_id AS 'suppid', c.nama AS 'namasupplier'
            				FROM ms_pembelian a LEFT JOIN lt_hutang b
            				ON a.id = b.pembelian_id
            				LEFT JOIN ms_supplier c
            				ON a.supplier_id = c.supplier_id
            				WHERE a.status = 'A'
            				$date_sc $nota_sc $id_sc $supp_id_sc ");

                            $arraydata = [$date_start_format,$date_end_format,$id,$nota,$supp_id];

                    // return to view
                    return view('finance/buying/buyinghome')->with('buyings', $buyings)->with('arraydata', $arraydata);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage debt export function------------------------------------------------------
        public function index_buying_export(Request $request)
        {
            //get request
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id             = $request->idadd;
            $nota           = $request->nota;
            $supp_id    = $request->supp_id;

            if($start != ""){
                //convert date
                $date_start = strtotime($start);
                $date_start_format = date("Y-m-d",$date_start);
                $date_end = strtotime($end);
                $date_end_format = date("Y-m-d",$date_end);
            }else{
                $date_start_format = $start;
                $date_end_format = $end;
            }

            $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $nota_sc    = $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
            $id_sc      = $id != "" ? "AND a.pembelian_id LIKE '%".$id."%'" : "";
            $supp_id_sc = $supp_id != "" ? "AND a.supplier_id LIKE '%".$supp_id."%'" : "";

            $buyings = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*, b.status_hutang AS 'status_hutang', c.supplier_id AS 'suppid', c.nama AS 'namasupplier'
            FROM ms_pembelian a LEFT JOIN lt_hutang b
            ON a.id = b.pembelian_id
            LEFT JOIN ms_supplier c
            ON a.supplier_id = c.supplier_id
            WHERE a.status = 'A'
            $date_sc $nota_sc $id_sc $supp_id_sc ");

            $arraydata = [$date_start_format,$date_end_format,$id,$nota,$supp_id];

            Excel::create('Data Buying : '.date("d-m-Y").'', function($result) use($buyings,$date_start_format,$date_end_format,$arraydata)
            {
                $result->sheet('Data Buying', function($sheet) use($buyings,$date_start_format,$date_end_format,$arraydata)
                {
                    $i = 1;
                    $count = 0;
                    foreach($buyings as $item){

                        $i++;
                        $count++;
                        $item->no_nota == "" ? $supplier = '' : $supplier = $item->suppid.'-'.$item->namasupplier;

                        if($item->status_hutang){
                            $item->status_hutang == 1 ? $statushut = 'UNPAID' : $statushut = 'PAID';
                        } else {
                            $statushut = 'PAID';
                        }

                        $data=[];

                        array_push($data, array(
                            $item->createddate,
                            $item->pembelian_id,
                            $item->tanggal,
                            $item->no_nota,
                            $supplier,
                            $statushut,
                            $item->tanggal_jatuh_tempo_pembelian,
                            $item->pembelian_dp,
                            $item->pembelian_bayar,
                            $item->pembelian_total
                        ));
                        
                        $sheet->fromArray($data, null, 'A10', false, false);
                    }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','BUYING REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));

                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }

                   $sheet->row(9, array('Created Date', 'Buying ID', 'Date Buy', 'Nota','Supplier ID', 'Status', 'Due Date','Down Payment', 'Paid', 'Total Price'));
                    $sheet->setBorder('A9:J9', 'thin');

                    // set style column
                    $sheet->cells('A9:J9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:J1');
                    $sheet->cells('A1:J1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':J'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);

                });
            })->download('xls');

            // return to view
            return view('finance/buying/buyinghome', ['buyings' => $buyings, 'arraydata' => $arraydata]);
        }
        // manage debt export function------------------------------------------------------

    // menu manage buying-------------------------------------------------------------------

    // menu manage debt---------------------------------------------------------------------

        // manage debt index function-------------------------------------------------------
        public function index_debt()
        {
            // query left join
                $lt_hutang = DB::select("SELECT a.*, b.pembelian_id AS 'b_pembelian_id', b.tanggal_jatuh_tempo_pembelian AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
                    FROM lt_hutang a LEFT JOIN ms_pembelian b
                    ON a.pembelian_id = b.id
                    WHERE a.status = 'A'
                    AND a.status_hutang <>'2'");

                $arraydata = ['','','','','all'];
            // return to view
            return view('finance/debt/debthome')->with('lt_hutang', $lt_hutang)->with('arraydata', $arraydata);
        }
        // ---------------------------------------------------------------------------------

        // manage debt edit function--------------------------------------------------------
        public function edit_debt($id)
        {
    		$lt_hutang = manage_debt::find($id);
            return view('finance/debt/debtedit', ['lt_hutang' => $lt_hutang]);
        }
        // ---------------------------------------------------------------------------------

        // manage debt do edit function-----------------------------------------------------
        public function do_edit_debt(Request $request)
        {
			$debt_id = $request->debt_id;
			$mustpaid = $request->mustpaid;

			$this->validate($request, [
				'pay'             => 'required|min:1|max:20'
			]);

			if($mustpaid<$request->pay){
				return redirect(url('finance/debt_home/edit/'.$debt_id))->with('error', ' Pay can not more than must paid .');
			}

			$hutang = manage_debt::find($debt_id);
			$hutang->bayar = $hutang->bayar + $request->pay;
			if($hutang->bayar==$hutang->total){
				$hutang->status_hutang = 0;
			}
			$hutang->save();

			$pembelian = manage_buying::find($hutang->pembelian_id);
			$data = deposit_supp::where('supplier_id', $pembelian->supplier_id)->first();
			$datafix = deposit_supp::find($data->id);
			$datafix->deposit                        	= $datafix->deposit-$request->pay;
			$datafix->last_modify_date                 = date('Y-m-d H:i:s');
			$datafix->modify_user_id                   = Auth::user()->karyawan_id;
			$datafix->save();

			$debthistory = new manage_debt_history;
			$debthistory->hutang_id 								= $hutang->id;
			$debthistory->total_hutang	 						= $hutang->total;
			$debthistory->total_pembayaran_hutang 	= $request->pay;
			$debthistory->created_date              = date('Y-m-d H:i:s');
			$debthistory->last_modify_date          = date('Y-m-d H:i:s');
			$debthistory->modify_user_id            = Auth::user()->karyawan_id;
			$debthistory->status                  	= 'A';
			$debthistory->save();

            return redirect(url('finance/debt_home'))->with('status', ' Updated credit has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage debt range function-------------------------------------------------------
        public function debt_range(Request $request)
        {
			//get request
			$start			= $request->dateStart;
			$end   			= $request->dateEnd;
			$idbuying		= $request->idbuying;
			$iddebt			= $request->iddebt;
			$status			= $request->status;

			// validate empty
			if($start == "" && $end == "" && $idbuying == "" &&  $iddebt == "" &&  $status == ""){
					return redirect(url('finance/debt_home'));
			}else{
				// validate return date if null
				if($start != ""){
						//convert date
						$date_start = strtotime($start);
						$date_start_format = date("Y-m-d",$date_start);
						$date_end = strtotime($end);
						$date_end_format = date("Y-m-d",$date_end);
				}else{
						$date_start_format = $start;
						$date_end_format = $end;
				}


				$date_sc    			= $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
				$idbuying_sc 			= $idbuying != "" ? "AND b.pembelian_id LIKE '%".$idbuying."%'" : "";
				$iddebt_sc				= $iddebt != "" ? "AND a.hutang_id LIKE '%".$iddebt."%'" : "";
				if($status=="unpaid"){
					$status_sc = $status != "" ? "AND a.status_hutang ='1'" : "";
				} else if($status=="paid"){
					$status_sc = $status != "" ? "AND a.status_hutang ='0'" : "";
				} else if($status=="all"){
					$status_sc = $status != "" ? "AND a.status_hutang <>'2'" : "";
				}

				$lt_hutang = DB::select("SELECT a.*, b.pembelian_id AS 'b_pembelian_id',
                                b.tanggal_jatuh_tempo_pembelian AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
								FROM lt_hutang a LEFT JOIN ms_pembelian b
								ON a.pembelian_id = b.id
								WHERE a.status = 'A'
								$date_sc $idbuying_sc $iddebt_sc $status_sc ");


				$arraydata = [$date_start_format,$date_end_format,$idbuying,$iddebt,$status];

				// return to view
				return view('finance/debt/debthome')->with('lt_hutang', $lt_hutang)->with('arraydata', $arraydata);
			}
        }
        // ---------------------------------------------------------------------------------

        // manage debt export function------------------------------------------------------
        public function index_debt_export(Request $request)
        {
            //get request
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idbuying       = $request->idbuying;
            $iddebt         = $request->iddebt;
            $status         = $request->status;

            // validate return date if null
            if($start != ""){
                //convert date
                $date_start = strtotime($start);
                $date_start_format = date("Y-m-d",$date_start);
                $date_end = strtotime($end);
                $date_end_format = date("Y-m-d",$date_end);
            }else{
                $date_start_format = $start;
                $date_end_format = $end;
            }

            $date_sc                = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $idbuying_sc            = $idbuying != "" ? "AND b.pembelian_id LIKE '%".$idbuying."%'" : "";
            $iddebt_sc              = $iddebt != "" ? "AND a.hutang_id LIKE '%".$iddebt."%'" : "";

            if($status=="unpaid"){
                $status_sc = $status != "" ? "AND a.status_hutang ='1'" : "";
            } else if($status=="paid"){
                $status_sc = $status != "" ? "AND a.status_hutang ='0'" : "";
            } else if($status=="all"){
                $status_sc = $status != "" ? "AND a.status_hutang <>'2'" : "";
            }

            $lt_hutang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                        b.pembelian_id AS 'b_pembelian_id', b.tanggal_jatuh_tempo_pembelian AS 'jatuhtempo',
                        b.modify_user_id AS 'modify_user_id'
                        FROM lt_hutang a LEFT JOIN ms_pembelian b
                        ON a.pembelian_id = b.id
                        WHERE a.status = 'A'
                        $date_sc $idbuying_sc $iddebt_sc $status_sc ");

            $arraydata = [$date_start_format,$date_end_format,$idbuying,$iddebt,$status];

            Excel::create('Data Debt : '.date("d-m-Y").'', function($result) use($lt_hutang,$arraydata)
            {
                $result->sheet('Data Debt', function($sheet) use($lt_hutang,$arraydata)
                {
                    $count = 0;
                    $i = 1;
                    foreach($lt_hutang as $item){
                        $i++;
                        $count++;
                        $item->status_hutang > 0 ? $statushut = 'UNPAID' : $statushut = 'PAID';
                        $data=[];

                        array_push($data, array(
                            $item->createddate,
                            $item->hutang_id,
                            $item->b_pembelian_id,
                            $item->total,
                            $item->bayar,
                            $statushut
                        ));
                        
                        $sheet->fromArray($data, null, 'A10', false, false);
                    }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','DEBT REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }
                    $sheet->row(9, array('Created Date', 'Debt ID', 'Buying ID', 'Total', 'Paid', 'Status Debt'));
                    $sheet->setBorder('A9:F9', 'thin');

                    // set style column
                    $sheet->cells('A9:F9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:F1');
                    $sheet->cells('A1:F1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':F'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                        $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                });
            })->download('xls');

            // return to view
            return view('finance/debt/debthome', ['lt_hutang' => $lt_hutang, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage debt---------------------------------------------------------------------

    // menu purchase return-----------------------------------------------------------------

        // purchase return index function---------------------------------------------------
        public function index_return()
        {
            $tr_returpembelian = DB::select("SELECT a.*, b.pembelian_id AS 'b_pembelian_id', SUM(c.qty) AS 'jumlahqty'
            			FROM ms_returpembelian a LEFT JOIN ms_pembelian b
            			ON a.pembelian_id = b.id
            			LEFT JOIN tr_returpembelian c
            			ON a.id = c.returpembelian_id
            			WHERE a.status = 'A'
            			GROUP BY a.id");

            // return to view
            return view('finance/purchase/returnhome', ['tr_returpembelian' => $tr_returpembelian]);
        }
        // ---------------------------------------------------------------------------------

        // purchase return select2 buying function------------------------------------------
        public function search_buying()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_pembelian = DB::table('ms_pembelian')
                            ->select('ms_pembelian.*','tr_detail_pembelian.barang_id AS items_id','tr_detail_pembelian.qty AS qty','tr_detail_pembelian.sub_total_pembelian AS sub_total')
                            ->leftJoin('tr_detail_pembelian', 'ms_pembelian.pembelian_id', '=', 'tr_detail_pembelian.ms_pembelian_id')
                            ->leftJoin('ms_barang', 'tr_detail_pembelian.barang_id', '=', 'ms_barang.barang_id')
                            ->where('ms_pembelian.status',"A")
														->whereNotNull('ms_pembelian.no_nota')
                            ->where('ms_pembelian.pembelian_id','like', "%".$term."%" )
                            ->orWhere('ms_pembelian.no_nota','like', "%".$term."%" )
                            ->orWhere('ms_pembelian.created_date','like', "%".$term."%" )
                            ->get();

            $query = $ms_pembelian;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->pembelian_id));
                    $new_row['text']=htmlentities(stripslashes($row->pembelian_id.' - '.$row->no_nota.' - '.date('d/m/Y', strtotime($row->created_date))));
                    $new_row['items_id']=htmlentities(stripslashes($row->items_id));
                    $new_row['qty']=htmlentities(stripslashes($row->qty));
                    $new_row['sub_total']=htmlentities(stripslashes($row->sub_total));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // purchase return add function-----------------------------------------------------
        public function add_return()
        {
        	$return = manage_returnpembelian::all();

        	if($return->isEmpty()){
        		$idreturn = "RB00000001";
        	} else {
        		//buat id
        		$returnn = manage_returnpembelian::orderBy('id', 'DESC')->first();
        		$lastnumber = substr($returnn->retur_id, 2, 8);
        		$idnumber = $lastnumber + 1;

        		$idreturn = "RB".sprintf("%08d", $idnumber);
        		//buat id
        	}

            return view('finance/purchase/returnadd')->with('idreturn', $idreturn);
        }
        // ---------------------------------------------------------------------------------

    	// purchase return do add function--------------------------------------------------
    	public function do_add_return(Request $request)
    	{
    		$return = manage_returnpembelian::all();
    		if($return->isEmpty()){
    			$idreturn = "RB00000001";
    		} else {
    			//buat id
    			$returnn = manage_returnpembelian::orderBy('id', 'DESC')->first();
    			$lastnumber = substr($returnn->retur_id, 2, 8);
    			$idnumber = $lastnumber + 1;

    			$idreturn = "RB".sprintf("%08d", $idnumber);
    			//buat id
    		}

    		$pembelian = manage_buying::where('pembelian_id', $request->buy_id)->first();
    		$retur = new manage_returnpembelian;
    		$retur->retur_id = $idreturn;
    		$retur->created_date = date('Y-m-d H:i:s');
    		$retur->pembelian_id = $pembelian->id;
    		$retur->status = 'A';
    		$retur->save();

    		return redirect(url('finance/return_home/addnext/'.$retur->id));
    	}
    	// ---------------------------------------------------------------------------------

    	// purchase return next return function---------------------------------------------
        public function next_return($id)
        {
    		$return = manage_returnpembelian::find($id);
    		$pembelian = manage_buying::find($return->pembelian_id);

    		return view('finance/purchase/returnadd_next')->with('return',$return)->with('pembelian',$pembelian);
    	}
        // ---------------------------------------------------------------------------------

        // purchase return next return get function-----------------------------------------
    	public function next_return_get($id)
    	{
    		$detailpembeian = manage_buying_history::find($id);

    		if($detailpembeian->status=="T"){
    			$barangtemp = manage_items_temp::find($detailpembeian->barang_id);
    			$data[] = array(
                'iditemsprimary' => $detailpembeian->barang_id,
                'items' => $barangtemp->nama,
                'subtotal' => $barangtemp->harga_beli,
                'qtyasli' => $detailpembeian->qty,
                'qtysudahreturn' => $detailpembeian->qty_return,
                'stockbarang' => $barangtemp->qty,
            );
    		} else {
    			$barang = manage_items::find($detailpembeian->barang_id);
    			$data[] = array(
                'iditemsprimary' => $detailpembeian->barang_id,
                'items' => $barang->nama,
                'subtotal' => $barang->harga,
                'qtyasli' => $detailpembeian->qty,
                'qtysudahreturn' => $detailpembeian->qty_return,
                'stockbarang' => $barang->stock,
            );
    		}
            return json_encode($data);
    	}
        // ---------------------------------------------------------------------------------

        // purchase return do add next function---------------------------------------------
        public function do_addnext_return(Request $request)
        {
            $last_qty = $request->items_qty_view;

            if($request->items_qty_view > ($request->qtyasli-$request->qtysudahreturn)){
                return redirect(url('finance/return_home/addnext/'.$request->returid))->with('error', ' Quantities return must be less than last quantities has return.');
            }

    				$retur = manage_returnpembelian::find($request->returid);
    				$retur->total_return = $retur->total_return + $request->items_grand_total_view;
    				$retur->save();

    				$pembelian = manage_buying::find($retur->pembelian_id);
    				$deposittt = deposit_supp::where('supplier_id', $pembelian->supplier_id)->first();
    				if($request->type_return==1){
    						if($deposittt->deposit==0){
    							return redirect(url('finance/return_home/addnext/'.$request->returid))->with('error', ' Can not use CUT THE DEBT as Type Return because Deposit is 0.');
    						}
    				}

    				$pembelian_detail = manage_buying_history::find($request->detailpembelianid);

            $data = new manage_purchase_return;
            $data->returpembelian_id           	= $request->returid;
            $data->barang_id                    = $request->iditemsprimary;
            $data->qty                          = $request->items_qty_view;
            $data->type_return                  = $request->type_return;
            $data->sub_total                 		= $request->subtotal;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
    				if($pembelian_detail->status=="T"){
    					$data->status                       = 'T';
    				} else {
    					$data->status                       = 'A';
    				}

            $data->save();


    				$pembelian_detail->qty_return = $pembelian_detail->qty_return + $request->items_qty_view;
    				$pembelian_detail->save();
						if($request->type_return!=3){
	    				if($pembelian_detail->status=="T"){
	    					$barang = manage_items_temp::find($request->iditemsprimary);
	    					$barang->qty = $request->stockbarang - $last_qty;
	    					$barang->save();
	    				} else {
	    					$barang = manage_items::find($request->iditemsprimary);
	    					$barang->stock = $request->stockbarang - $last_qty;
	    					$barang->save();
	    				}
						}

    				if($request->type_return==1 && $deposittt->deposit!=0){
    					return redirect(url('finance/return_home/addnext/cdb/'.$data->id.'/'.$request->returid.'/'.$request->items_grand_total_view));
    				} else if($request->type_return==4){
							return redirect(url('finance/buying_home/tig/add/'.$request->items_grand_total_view.'/'.$request->returid.'/'.$data->id));
						}

            return redirect(url('finance/return_home/addnext/'.$request->returid))->with('status', ' Created new purchase return has been success.');
        }
        // ---------------------------------------------------------------------------------

    	// purchase return add next cut the debt function-----------------------------------
    	public function addnext_cdb($detailid,$id,$total)
    	{
    		//mencari supplier_id
    		$retur = manage_returnpembelian::find($id);
    		$pembelian = manage_buying::find($retur->pembelian_id);
    		$supplier_id = $pembelian->supplier_id;
    		//mencari supplier_id

    		$lt_hutang = DB::table('lt_hutang')
    								->select('lt_hutang.*', 'ms_pembelian.supplier_id as supplier_id')
    								->leftJoin('ms_pembelian', 'lt_hutang.pembelian_id', 'ms_pembelian.id')
    								->where('lt_hutang.status', 'A')
    								->where('lt_hutang.status_hutang', '<>','0')
    								->where('lt_hutang.status_hutang', '<>', '2')
    								->get();

    		return view('finance/purchase/returnadd_next_cdb')->with('lt_hutang', $lt_hutang)
    																											->with('supplier_id', $supplier_id)
    																											->with('retur_id', $id)
    																											->with('detailid', $detailid)
    																											->with('total', $total);
    	}
    	// ---------------------------------------------------------------------------------

    	// purchase return do cut the debt function-----------------------------------------
    	public function do_addnext_cdb(Request $request)
    	{
    		$total_return = $request->total_return;
    		if(null == Input::get('cbpilih')){
    			return redirect(url('finance/return_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
    		} else {
    			foreach (Input::get('cbpilih') as $key => $hutangid) {
    				$hutang = manage_debt::find($key);
    				$lunas = $hutang->total-$hutang->bayar;
    				if($lunas <= $total_return){
    					$hutang->bayar = $hutang->total;
    					$total_return = $total_return - $lunas;
    					$hutang->status_hutang = 0;
    					$hutang->save();

							$debthistory = new manage_debt_history;
		    			$debthistory->hutang_id 								= $hutang->id;
		    			$debthistory->total_hutang	 						= $hutang->total;
		    			$debthistory->total_pembayaran_hutang 	= $lunas;
		    			$debthistory->created_date              = date('Y-m-d H:i:s');
		    			$debthistory->last_modify_date          = date('Y-m-d H:i:s');
		    			$debthistory->modify_user_id            = Auth::user()->karyawan_id;
		    			$debthistory->status                  	= 'A';
							$debthistory->returndetail_id       	= $request->detailid;
		    			$debthistory->save();

    				} else if($total_return==0){

    				} else {
    					$hutang->bayar = $hutang->bayar + $total_return;
    					$hutang->save();

							$debthistory = new manage_debt_history;
		    			$debthistory->hutang_id 								= $hutang->id;
		    			$debthistory->total_hutang	 						= $hutang->total;
		    			$debthistory->total_pembayaran_hutang 	= $total_return;
		    			$debthistory->created_date              = date('Y-m-d H:i:s');
		    			$debthistory->last_modify_date          = date('Y-m-d H:i:s');
		    			$debthistory->modify_user_id            = Auth::user()->karyawan_id;
		    			$debthistory->status                  	= 'A';
							$debthistory->returndetail_id       		= $request->detailid;
		    			$debthistory->save();

    					$total_return = 0;
    				}
    			}

    			$total_hutang = 0;
    			foreach (Input::get('cbpilih') as $key => $hutangid) {
    				$hutang = manage_debt::find($key);
    				$total_hutang += $hutang->total - $hutang->bayar;
    			}

    			$data = deposit_supp::where('supplier_id', $request->supplier_id)->first();
    			$datafix = deposit_supp::find($data->id);
    			$datafix->deposit                        	= $total_hutang;
    			$datafix->last_modify_date                 = date('Y-m-d H:i:s');
    			$datafix->modify_user_id                   = Auth::user()->karyawan_id;
    			$datafix->save();

    			return redirect(url('finance/return_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
    		}
    	}
    	// ---------------------------------------------------------------------------------

        // purchase return edit function----------------------------------------------------
        public function edit_return($id)
        {
            // query left join
            $tr_returpembelian = DB::table('tr_returpembelian')
                            ->select('tr_returpembelian.*','ms_pembelian.pembelian_total AS total_payment','ms_pembelian.tanggal AS payment_date','tr_detail_pembelian.qty AS quantities','tr_detail_pembelian.sub_total_pembelian AS sub_price','tr_detail_pembelian.stock_id AS stock_id', 'tr_detail_pembelian.items_id AS items_id')
                            ->leftJoin('ms_pembelian', 'tr_returpembelian.pembelian_id', '=', 'ms_pembelian.pembelian_id')
                            ->leftJoin('tr_detail_pembelian', 'tr_returpembelian.pembelian_id', '=', 'tr_detail_pembelian.detail_pembelian_id')
                            ->where('tr_returpembelian.status', 'A')
                        ->where('tr_returpembelian.id', $id)
                        ->first();

            return view('finance/purchase/returnedit', ['tr_returpembelian' => $tr_returpembelian]);
        }
        // ---------------------------------------------------------------------------------

        // purchase return do edit function-------------------------------------------------
        public function do_edit_return(Request $request, $id)
        {
            $this->validate($request, [
                'return_id'             => 'required',
                'type_return'           => 'required'
            ]);

            $data = manage_purchase_return::find($id);
            $data->retur_pembelian_id               = $request->return_id;
            $data->type_return                      = $request->type_return;

            $data->last_modify_date                 = date('Y-m-d H:i:s');
            $data->modify_user_id                   = Auth::user()->karyawan_id;
            $data->status                           = 'A';
            $data->save();

            return redirect(url('finance/return_home'))->with('status', ' Updated purchase return has been success.');
        }
        // ---------------------------------------------------------------------------------

        // purchase return delete function--------------------------------------------------
        public function delete_return($id)
        {
            $data = manage_returnpembelian::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            //$data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();
            return redirect(url('finance/return_home'))->with('status', ' Deleted purchase return has been success.');
        }
        // ---------------------------------------------------------------------------------

        // purchase return range function---------------------------------------------------
        public function return_range(Request $request)
        {
			//get tanggal
			$start 		= $request->dateStart;
			$end   		= $request->dateEnd;
			$idreturn	= $request->idreturn;
			$idbuying	= $request->idbuying;
			$total		= $request->total ;

			// validate empty
			if($start == "" && $end == "" && $idreturn == "" &&  $idbuying == "" &&  $total == ""){
					return redirect(url('finance/return_home'));
			}else{
			
            // validate return date if null
			if($start != ""){
				//convert date
				$date_start = strtotime($start);
				$date_start_format = date("Y-m-d",$date_start);
				$date_end = strtotime($end);
				$date_end_format = date("Y-m-d",$date_end);
			}else{
				$date_start_format = $start;
				$date_end_format = $end;
			}

			$date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
			$idreturn_sc = $idreturn != "" ? "AND a.retur_id LIKE '%".$idreturn."%'" : "";
			$idbuying_sc = $idbuying != "" ? "AND b.pembelian_id LIKE '%".$idbuying."%'" : "";
			$total_sc = $total != "" ? "AND a.total_return LIKE '%".$total."%'" : "";

			$tr_returpembelian = DB::select("SELECT a.*, b.pembelian_id AS 'b_pembelian_id', SUM(c.qty) AS 'jumlahqty'
											FROM ms_returpembelian a LEFT JOIN ms_pembelian b
											ON a.pembelian_id = b.id
											LEFT JOIN tr_returpembelian c
											ON a.id = c.returpembelian_id
											WHERE a.status = 'A'
											$date_sc $idreturn_sc $idbuying_sc $total_sc
											GROUP BY a.id");

    			$arraydata = [$date_start_format,$date_end_format,$idreturn,$idbuying,$total];

    			// return to view
    			return view('finance/purchase/returnhome')->with('tr_returpembelian',$tr_returpembelian)->with('arraydata',$arraydata);
            }
        }
        // ---------------------------------------------------------------------------------

        // purchase return export function--------------------------------------------------
        public function index_return_export(Request $request)
        {
            //get tanggal
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idreturn       = $request->idreturn;
            $idbuying       = $request->idbuying;
            $total          = $request->total;

            // validate return date if null
            if($start != ""){
                //convert date
                $date_start = strtotime($start);
                $date_start_format = date("Y-m-d",$date_start);
                $date_end = strtotime($end);
                $date_end_format = date("Y-m-d",$date_end);
            }else{
                $date_start_format = $start;
                $date_end_format = $end;
            }

            $date_sc      = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $idreturn_sc  = $idreturn != "" ? "AND a.retur_id LIKE '%".$idreturn."%'" : "";
            $idbuying_sc  = $idbuying != "" ? "AND b.pembelian_id LIKE '%".$idbuying."%'" : "";
            $total_sc     = $total != "" ? "AND a.total_return LIKE '%".$total."%'" : "";

            $tr_returpembelian = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                                a.*, b.pembelian_id AS 'b_pembelian_id', SUM(c.qty) AS 'jumlahqty'
                                FROM ms_returpembelian a LEFT JOIN ms_pembelian b
                                ON a.pembelian_id = b.id
                                LEFT JOIN tr_returpembelian c
                                ON a.id = c.returpembelian_id
                                WHERE a.status = 'A'
                                $date_sc $idreturn_sc $idbuying_sc $total_sc
                                GROUP BY a.id");

            $arraydata = [$date_start_format,$date_end_format,$idreturn,$idbuying,$total];

            Excel::create('Data Purchase Return : '.date("d-m-Y").'', function($result) use($tr_returpembelian,$arraydata)
            {
                $result->sheet('Data Purchase Return', function($sheet) use($tr_returpembelian,$arraydata)
                {
                    $i = 1;
                    $count = 0;
                    foreach($tr_returpembelian as $item){
                        $i++;
                        $count++;
                        $data=[];
                        array_push($data, array(
                            $item->createddate,
                            $item->retur_id,
                            $item->b_pembelian_id,
                            $item->jumlahqty,
                            $item->total_return,
                            $item->status
                        ));
                        $sheet->fromArray($data, null, 'A10', false, false);
                    }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','PURCHASE RETURN REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }
                    $sheet->row(9, array('Created Date','ID Purchase Return','Buying ID','Total QTY','Total','Status'));
                    $sheet->setBorder('A9:F9', 'thin');

                    // set style column
                    $sheet->cells('A9:F9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:F1');
                    $sheet->cells('A1:F1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':F'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                });
            })->download('xls');

            // return to view
            return view('finance/purchase/returnhome', ['tr_returpembelian' => $tr_returpembelian, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu purchase return-----------------------------------------------------------------

    // menu manage selling------------------------------------------------------------------

        // manage selling index function----------------------------------------------------
        public function index_selling()
        {
            // query left join
            $sellings = DB::select("SELECT a.*, b.status_piutang AS 'status_piutang',
                    c.customer_id AS 'custid', c.nama AS 'namacustomer'
                    FROM ms_penjualan a LEFT JOIN lt_piutang b
                    ON a.id = b.penjualan_id
                    LEFT JOIN ms_customer c
                    ON a.customer_id = c.customer_id
                    WHERE a.status = 'A'");

            // return to view
            return view('finance/selling/sellinghome', ['sellings' => $sellings]);
        }
        // ---------------------------------------------------------------------------------


        // manage selling select2 items function--------------------------------------------
        public function search_items_selling()
        {
            $row_set = [];
            $type = strip_tags(trim($_GET['type']));
            if($type==2){
            $term = strip_tags(trim($_GET['q']));

            $ms_barang = DB::table('ms_barang')
                            ->select('ms_barang.*', 'tr_stokbarang.stock AS qty_buy', 'tr_stokbarang.id AS id_stock', 'ms_kategori_barang.is_inventory AS inventory')
                            ->leftJoin('tr_stokbarang', 'ms_barang.barang_id', '=', 'tr_stokbarang.barang_id')
                            ->leftJoin('ms_kategori_barang', 'ms_barang.kategori', '=', 'ms_kategori_barang.id')
                            ->where('ms_barang.status',"A")
                            ->where('ms_barang.is_available',1)
														->where('ms_barang.stock','>','0')
                            ->where('ms_barang.barang_id','like', "%".$term."%" )
                            ->orWhere('ms_barang.nama','like', "%".$term."%" )
                            ->orWhere('ms_barang.created_date','like', "%".$term."%" )
                            ->where('ms_barang.status',"A")
                            ->where('ms_barang.is_available',1)
                            ->get();

            $query = $ms_barang;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->id));
                    $new_row['text']=htmlentities(stripslashes($row->barang_id ." - ". $row->nama ." - ". date('d/m/Y', strtotime($row->created_date))));
                    $new_row['qty']=htmlentities(stripslashes($row->stock));
                    $new_row['qty_buy']=htmlentities(stripslashes($row->qty_buy));
                    $new_row['sub_total']=htmlentities(stripslashes($row->harga));
                    $new_row['sell_price']=htmlentities(stripslashes($row->harga_jual));
                    $new_row['id_get_items']=htmlentities(stripslashes($row->id));
                    $new_row['id_get_stock']=htmlentities(stripslashes($row->id_stock));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $new_row['inven']=htmlentities(stripslashes($row->inventory));
										$new_row['isavailable']=htmlentities(stripslashes($row->is_available));
                    $row_set[] = $new_row; //build an array
                }

            }

            }else{

                $term = strip_tags(trim($_GET['q']));

                $ms_jasa = DB::table('ms_jasa')
                                ->select('ms_jasa.*')
                                ->where('ms_jasa.status',"A")
																->where('ms_jasa.qty','>','0')
                                ->where('ms_jasa.service_id','like', "%".$term."%" )
                                ->orWhere('ms_jasa.name','like', "%".$term."%" )
                                ->orWhere('ms_jasa.created_date','like', "%".$term."%" )
                                ->get();

                $query = $ms_jasa;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->id));
                        $new_row['text']=htmlentities(stripslashes($row->service_id ." - ". $row->name ." - ". date('d/m/Y', strtotime($row->created_date))));
                        $new_row['qty']=htmlentities(stripslashes($row->qty));
                        $new_row['qty_buy']=htmlentities(stripslashes(0));
                        $new_row['sell_price']=htmlentities(stripslashes($row->price));
                        $new_row['id_get_items']=htmlentities(stripslashes(0));
                        $new_row['id_get_stock']=htmlentities(stripslashes(0));
                        $new_row['name']=htmlentities(stripslashes($row->name));
                        $row_set[] = $new_row; //build an array
                    }
                }

            }


            //tambah add new untuk tambah baru
            $new_row['id']="addnew";
            $new_row['text']="Other";
            $new_row['qty']="0";
            $new_row['qty_buy']="0";
            $new_row['sub_total']="0";
            $new_row['id_get_items']="0";
            $new_row['id_get_stock']="0";
            $row_set[] = $new_row; //build an array

            return json_encode($row_set); //format the array into json data
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 customer function-----------------------------------------
        public function search_customer_selling()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_customer = DB::table('ms_customer')
                            ->select('ms_customer.*','tr_deposit_customer.deposit AS deposit')
                            ->leftJoin('tr_deposit_customer', 'ms_customer.customer_id', '=', 'tr_deposit_customer.customer_id')
                            ->where('ms_customer.status',"A")
                            ->where('ms_customer.nama','like', "%".$term."%" )
                            ->orWhere('ms_customer.customer_id','like', "%".$term."%" )
                            ->get();

            $query = $ms_customer;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->customer_id));
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->customer_id));
                    $new_row['deposit']=htmlentities(stripslashes($row->deposit));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 employee 1 function---------------------------------------
        public function search_employee_1()
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
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 employee 2 function---------------------------------------
        public function search_employee_2()
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
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 employee 3 function---------------------------------------
        public function search_employee_3()
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
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 employee 4 function---------------------------------------
        public function search_employee_4()
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
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling select2 employee 5 function---------------------------------------
        public function search_employee_5()
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
                    $new_row['text']=htmlentities(stripslashes($row->nama ." - ". $row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling add function------------------------------------------------------
        public function add_selling()
        {
    	    $validasi = 0;
            return view('finance/selling/sellingadd')->with('validasi', $validasi);
        }
        // ---------------------------------------------------------------------------------

        // manage selling do add function---------------------------------------------------
        public function do_add_selling(Request $request)
        {
            $this->validate($request, [
                'items_id'              => 'required|min:1',
                'type_select'           => 'required|max:100',
                'items_qty_view'        => 'required',
                'items_sub_total_view'  => 'required|max:20'
            ]);

    		$type = $request->type_id;
    		//buying id validasi----------------------------------------------------------------------
    		if($request->validasi==0){
    			//$msid = $data->id;
    		} else {
    			$msid = $request->id_penjualan;
    		}
    		//buying id validasi----------------------------------------------------------------------

    		if($request->items_id!="addnew"){
				if($request->items_qty_view > $request->items_qty_viewasli){
					if($request->validasi==0){
						return redirect(url('finance/selling_home/add'))->with('error', ' Quantities not Enough.');
					} else {
						return redirect(url('finance/selling_home/addnext/'.$request->id_penjualan))->with('error', ' Quantities not Enough.');
					}
				}
    		}

    		// calculate total pembelian-------------------------------------------------------------
    		$total_price = $request->items_qty_view * $request->items_sub_total_view;
    		// calculate total pembelian-------------------------------------------------------------

    		// validasi quantities-------------------------------------------------------------------
    		if($total_price == 0){
    			if($request->validasi==0){
    				return redirect(url('finance/selling_home/add'))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    			} else {
    				return redirect(url('finance/selling_home/addnext/'.$msid))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    			}
    		}
    		// validasi quantities-------------------------------------------------------------------

    		// validasi saat unpaid dan paid---------------------------------------------------------
    		if($request->validasi==0){
    			//buat id
    			$selling = manage_selling::orderBy('id', 'DESC')->first();
    			if($selling){
    				$lastnumber = substr($selling->penjualan_id, 1, 9);
    				$idnumber = $lastnumber + 1;

    				$sellingid = "S".sprintf("%09d", $idnumber);
    			} else {
    				$sellingid = "S000000001";
    			}

    			//buat id
    			// save to ms_penjualan------------------------------------------------------------------
    			$data = new manage_selling;
    			$data->penjualan_id                     = $sellingid;
    			// $data->tanggal                          = $request->buy_date;
    			$data->created_date                     = date('Y-m-d H:i:s');
    			$data->last_modify_date                 = date('Y-m-d H:i:s');
    			$data->modify_user_id                   = Auth::user()->karyawan_id;
    			$data->status                           = 'A';
    			$data->save();
    		}
    		// save to ms_pembelian-------------------------------------------------------------------

    		//buying id validasi----------------------------------------------------------------------
    		if($request->validasi==0){
    			$msid = $data->id;
    		} else {
    			$msid = $request->id_penjualan;
    		}
    		//buying id validasi----------------------------------------------------------------------

    		// save to tr_detail_pembelian------------------------------------------------------------
    		if($request->items_id=="addnew"){
    			$datatemp = new manage_items_temp;
    			$datatemp->nama = $request->new_item;
    			$datatemp->qty = $request->items_qty_view;
    			$datatemp->harga_jual = $request->items_sub_total_view;
    			$datatemp->status = 'T';
    			$datatemp->created_date = date('Y-m-d H:i:s');
    			$datatemp->save();

    			$datadetail = new manage_selling_history;
    			$datadetail->detail_penjualan_id             	= $msid;
    			$datadetail->barang_id                        = $datatemp->id;
    			$datadetail->qty                              = $request->items_qty_view;
    			$datadetail->type_sell                        = $type;
    			$datadetail->sub_total_penjualan              = $request->items_sub_total_view;
    			$datadetail->created_date                     = date('Y-m-d H:i:s');
    			$datadetail->last_modify_date                 = date('Y-m-d H:i:s');
    			$datadetail->modify_user_id                   = Auth::user()->karyawan_id;
    			$datadetail->status                           = 'T';
    			$datadetail->id_karyawan_kerja1         						= $request->emp1_id;
                $datadetail->id_karyawan_kerja2         						= $request->emp2_id;
                $datadetail->id_karyawan_kerja3         						= $request->emp3_id;
                $datadetail->id_karyawan_kerja4         						= $request->emp4_id;
                $datadetail->id_karyawan_kerja5         						= $request->emp5_id;
    			$datadetail->save();

    		} else {
    			$datadetail = new manage_selling_history;
    			$datadetail->detail_penjualan_id             	 = $msid;
    			$datadetail->barang_id                        = $request->items_id;
    			$datadetail->qty                              = $request->items_qty_view;
    			$datadetail->type_sell                        = $type;
    			$datadetail->sub_total_penjualan              = $request->items_sub_total_view;
    			$datadetail->created_date                     = date('Y-m-d H:i:s');
    			$datadetail->last_modify_date                 = date('Y-m-d H:i:s');
    			$datadetail->modify_user_id                   = Auth::user()->karyawan_id;
    			$datadetail->status                           = 'A';
    			$datadetail->id_karyawan_kerja1         						= $request->emp1_id;
                $datadetail->id_karyawan_kerja2         						= $request->emp2_id;
                $datadetail->id_karyawan_kerja3         						= $request->emp3_id;
                $datadetail->id_karyawan_kerja4         						= $request->emp4_id;
                $datadetail->id_karyawan_kerja5         						= $request->emp5_id;
    			$datadetail->save();
    		}
    		// save to tr_detail_pembelian------------------------------------------------------------

    		return redirect(url('finance/selling_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage selling do add next function----------------------------------------------
        public function do_add_next_selling(Request $request)
        {

            $this->validate($request, [
                'items_id'              => 'required|min:1',
                'type_select'           => 'required|max:100',
                'items_qty_view'        => 'required',
                'items_sub_total_view'  => 'required|max:20'
            ]);

            if($request->items_id!="addnew"){
                if($request->items_qty_view > $request->items_qty_viewasli){
                    return redirect(url('finance/selling_home/add'))->with('error', ' Quantities not Enough.');
                }
            }

             // save to detail penjualan
            $data_history = new manage_selling_history;
            $data_history->detail_penjualan_id        = $request->penjualan_id;
            $data_history->barang_id                  = ($request->items_id=="addnew")?$request->new_item:$request->items_id;
            $data_history->items_id                   = $request->id_get_items;
            $data_history->stock_id                   = $request->id_get_stock;
            $data_history->qty                        = $request->sell_qty;
            $data_history->sub_total_penjualan        = ($request->items_id=="addnew")?$request->new_price:$request->items_sub_total;

            $data_history->id_karyawan_kerja1         = $request->emp1_id;
            $data_history->id_karyawan_kerja2         = $request->emp2_id;
            $data_history->id_karyawan_kerja3         = $request->emp3_id;
            $data_history->type_sell                  = $request->type_id;
            $data_history->id_karyawan_kerja4         = $request->emp4_id;
            $data_history->id_karyawan_kerja5         = $request->emp5_id;

            $data_history->jasa_penjualan             = $request->sell_price;
            $data_history->keterangan_jasa_penjualan  = $request->sell_desc;

            $data_history->created_date               = date('Y-m-d H:i:s');
            $data_history->last_modify_date           = date('Y-m-d H:i:s');
            $data_history->modify_user_id             = Auth::user()->karyawan_id;
            $data_history->status                     = 'T';
            $data_history->save();



            // return to view
            return redirect(url('finance/selling_home/add_next',[$request->penjualan_id]));
        }
        // ---------------------------------------------------------------------------------

        // manage selling add next function-------------------------------------------------
        public function addnext_selling($id)
        {
    		$selling = manage_selling::find($id);
    		$validasi = 1;

    		//menghitung total------------------------------------------------------------------------
    		$total = 0;
    		$details = manage_selling_history::where('detail_penjualan_id', $id)->get();
    		foreach ($details as $detail) {
    			$total += $detail->sub_total_penjualan * $detail->qty;
    		}
    		//menghitung total------------------------------------------------------------------------

            return view('finance/selling/sellingaddnext')->with('selling', $selling)->with('validasi', $validasi)->with('total', $total);
        }
        // ---------------------------------------------------------------------------------

        // manage selling get grand total function------------------------------------------
        function get_grand_total($id,$format=true)
        {
            $data = manage_selling_history::where('detail_penjualan_id',$id)->get();
            $total = 0;
            foreach ($data as $key) {
                $total += ($key->sub_total_penjualan*$key->qty);
            }
            if($format){
                return number_format($total,0,"",".");
            }else{
                return number_format($total,0,"","");
            }
        }
        // ---------------------------------------------------------------------------------

        // manage selling delete detail function--------------------------------------------
        public function delete_selling_detail($detailid,$sellingid)
        {
					$detail = manage_selling_history::find($detailid);
					manage_items_temp::destroy($detail->barang_id);
					manage_selling_history::destroy($detailid);
					return redirect(url('finance/selling_home/addnext/'.$sellingid))->with('status', ' Deleted buying has been success.');
        }
        // ---------------------------------------------------------------------------------

    	// manage selling checkout function-------------------------------------------------
    	public function index_selling_checkout($id)
    	{
    		$selling = manage_selling::find($id);

    		//menghitung total------------------------------------------------------------------------
    		$total = 0;
    		$details = manage_selling_history::where('detail_penjualan_id', $id)->get();
    		foreach ($details as $detail) {
    			$total += $detail->sub_total_penjualan * $detail->qty;
    		}
    		//menghitung total------------------------------------------------------------------------

    		return view('finance/selling/sellingcheckout')->with('total', $total)
    																					->with('selling', $selling);
    	}
    	// ---------------------------------------------------------------------------------

    	// manage selling do checkout function----------------------------------------------
    	public function index_selling_docheckout(Request $request)
    	{
            if($request->status_payment != 1){
                $this->validate($request, [
                    'cust_id'           => 'required|min:1',
                    'status_payment'    => 'required|min:1',
                    'sell_dp'           => 'required|max:20',
                    'sell_due_date'     => 'required'
                ]);
            }else{
                $this->validate($request, [
                    'cust_id'           => 'required|min:1',
                    'status_payment'    => 'required|min:1'
                ]);
            }

    		$selling = manage_selling::find($request->sellingid);

    		//menghitung total------------------------------------------------------------------------
    		$total_price = 0;
    		$details = manage_selling_history::where('detail_penjualan_id', $request->sellingid)->get();
    		foreach ($details as $detail) {
    			$total_price += $detail->sub_total_penjualan * $detail->qty;
    			// // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
    			if($detail->status!="T"){
    				if($detail->type_sell==2){
    					$item = manage_items::find($detail->barang_id);
    					$item->stock = $item->stock - $detail->qty;
    					$item->is_available = 1;
    					$item->last_modify_date = date('Y-m-d H:i:s');
    					$item->modify_user_id = Auth::user()->karyawan_id;
    					$item->status = 'A';
    					$item->save();
    					$penjualan_detail = manage_selling_history::find($detail->id);
    					if($penjualan_detail->sub_total_penjualan>$item->harga_jual){
    						$penjualan_detail->type_report = 1;
    					} else if($penjualan_detail->sub_total_penjualan==$item->harga_jual){
    						$penjualan_detail->type_report = 2;
    					} else {
								$penjualan_detail->type_report = 0;
							}
    					$penjualan_detail->save();
    				} else {
    					$item = manage_service::find($detail->barang_id);
    					$item->qty = $item->qty - $detail->qty;
    					$item->last_modify_date = date('Y-m-d H:i:s');
    					$item->modify_user_id = Auth::user()->karyawan_id;
    					$item->status = 'A';
    					$item->save();
    					$penjualan_detail = manage_selling_history::find($detail->id);
    					if($penjualan_detail->sub_total_penjualan>$item->price){
    						$penjualan_detail->type_report = 1;
    					} else if($penjualan_detail->sub_total_penjualan==$item->harga_jual){
    						$penjualan_detail->type_report = 2;
    					} else {
								$penjualan_detail->type_report = 0;
							}
    					$penjualan_detail->save();
    				}
    			} else if($detail->status=="T"){
							$datatemp = manage_items_temp::find($detail->barang_id);
		    			$datatemp->status = 'A';
		    			$datatemp->save();
					}
    			// // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
    		}
    		//menghitung total------------------------------------------------------------------------

    		// set format id hutang------------------------------------------------------------------
    		$for_date_cred      = date("dmy",strtotime($request->buy_date));
    		$for_sell_id         = substr($selling->penjualan_id,0,10);

    		$format_cred_id = $for_sell_id .'-'. $for_date_cred;
    		// set format id hutang------------------------------------------------------------------

    		// // validasi saat unpaid dan paid---------------------------------------------------------
    		if($request->status_payment != '1'){
    		    if($request->sell_dp > $total_price){
    		        return redirect(url('finance/selling_home/checkout'))->with('error', ' Down payment must less than Grand Total');
    		    }

    		    if($request->sell_dp == $total_price){
    		        return redirect(url('finance/selling_home/checkout'))->with('error', ' Down payment must less than Grand Total');
    		    }
    		}
    		// validasi saat unpaid dan paid---------------------------------------------------------

    		// Edit to ms_pembelian------------------------------------------------------------------
    			$data = manage_selling::find($request->sellingid);
    			if($request->no_nota != ''){
                    $data->no_nota                          = $request->no_nota;
                }else{
                    $data->no_nota                          = 'NOTAUNSET_'.$request->sellingid;
                }
    			$data->customer_id                      = $request->cust_id;
    			$data->status_bayar_penjualan           = $request->status_payment;
    			$data->tanggal                     			= date('Y-m-d H:i:s');
					$data->status_transaksi                 = 1;
    			if($request->status_payment != '0'){
    			    $data->penjualan_bayar                  = $total_price;
    			    $data->tanggal_jatuh_tempo_penjualan    = date('Y-m-d H:i:s');
    			    $data->penjualan_dp                     = 0;
    			}else{
    			    $data->penjualan_bayar                  = $request->sell_dp;
    			    $data->tanggal_jatuh_tempo_penjualan    = $request->sell_due_date;
    			    $data->penjualan_dp                     = $request->sell_dp;
    			}
    			$data->penjualan_total                  = $total_price;
    			$data->save();
    		// Edit to ms_pembelian-------------------------------------------------------------------

    		//cek manage isinya//
    		$creddd = manage_credit::orderBy('id', 'DESC')->first();
    		if($creddd){
    			$lastnumber = substr($creddd->piutang_id, 3, 7);
    			$idnumber = $lastnumber + 1;

    			$credddid = "CRE".sprintf("%07d", $idnumber);
    		} else {
    			$credddid = "CRE0000001";
    		}
    		//cek manage isinya//

    		// // save ke lt_hutang-----------------------------------------------------------------------
    		$data = new manage_credit;
    		$data->piutang_id                        = $credddid;
    		$data->penjualan_id                     = $request->sellingid;

    		// jika pembelian langsung lunas
    		if($request->status_payment > 0){
    		    $data->total                        = 0;
    		    $data->bayar                        = 0;
    		    $data->status_piutang                = 2;
    		// jika pembelian tidak langsung lunas
    		}else if($request->status_payment < 1){
    		    $data->total                        = $total_price;
    		    $data->bayar                        = $request->sell_dp;
    		    $data->status_piutang                = 1;
    		}

    		$data->created_date                     = date('Y-m-d H:i:s');
    		$data->last_modify_date                 = date('Y-m-d H:i:s');
    		$data->modify_user_id                   = Auth::user()->karyawan_id;
    		$data->status                           = 'A';
    		$data->save();
    		// save ke lt_hutang-----------------------------------------------------------------------

    		// update total hutang ke tr_deposit_customer ---------------------------------------------
    		if($request->status_payment < 1){
    		    deposit_cust::where('customer_id',$request->cust_id)->update([
    		        'deposit' => ($total_price - $request->sell_dp) + $request->cust_deposit,
    		        'last_modify_date' => date('Y-m-d H:i:s'),
    		        'modify_user_id' => Auth::user()->karyawan_id,
    		        'status' => 'A'
    		    ]);
    		}
    		// update total hutang ke tr_deposit_customer ---------------------------------------------

				if($request->status_payment < 1){
					$credithistory = new manage_credit_history;
					$credithistory->piutang_id 				  = $data->id;
					$credithistory->total_piutang	 		  = $total_price;
					$credithistory->total_pembayaran_piutang  = $request->sell_dp;;
					$credithistory->created_date              = date('Y-m-d H:i:s');
					$credithistory->last_modify_date          = date('Y-m-d H:i:s');
					$credithistory->modify_user_id            = Auth::user()->karyawan_id;
					$credithistory->status                  	= 'A';
					$credithistory->save();
				}

    		return redirect(url('finance/selling_home'));
    	}
    	// ---------------------------------------------------------------------------------

        // manage selling edit function-----------------------------------------------------
        public function edit_selling($id)
        {
    		$selling = manage_selling::find($id);

    		//menghitung total------------------------------------------------------------------------
    		$total = 0;
    		$details = manage_selling_history::where('detail_penjualan_id', $id)->get();
    		foreach ($details as $detail) {
    			$total += $detail->sub_total_penjualan * $detail->qty;
    		}
    		//menghitung total------------------------------------------------------------------------

            return view('finance/selling/sellingedit', ['selling' => $selling, 'total' => $total]);
        }
        // ---------------------------------------------------------------------------------

        // manage selling do edit function--------------------------------------------------
        public function do_edit_selling(Request $request, $id)
        {
    		$this->validate($request, [
                'no_nota'          => 'required|max:100'
            ]);

            $data = manage_selling::find($id);
            $data->no_nota                          = $request->no_nota;
            $data->last_modify_date                 = date('Y-m-d H:i:s');
            $data->modify_user_id                   = Auth::user()->karyawan_id;
            $data->status                           = 'A';
            $data->save();

            return redirect(url('finance/selling_home'))->with('status', ' Updated buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage selling delete function---------------------------------------------------
        public function delete_selling($id)
        {
			$data = manage_selling::find($id);
			$data->deleted_date     = date('Y-m-d H:i:s');
			$data->modify_user_id   = Auth::user()->karyawan_id;
			$data->status           = 'D';
			$data->save();

			$datadetail = manage_selling_history::where('detail_penjualan_id',$id)->get();
			foreach ($datadetail as $row) {
				$datadetaill = manage_selling_history::find($row->id);
				$datadetaill->deleted_date     = date('Y-m-d H:i:s');
				$datadetaill->modify_user_id   = Auth::user()->karyawan_id;
				$datadetaill->status           = 'D';
				$datadetaill->save();
			}

			$creditt = manage_credit::where('penjualan_id',$data->id)->first();
			if($creditt){
				$credit = manage_credit::find($creditt->id);
				$credit->deleted_date     = date('Y-m-d H:i:s');
				$credit->modify_user_id   = Auth::user()->karyawan_id;
				$credit->status           = 'D';
				$credit->save();
			}

			return redirect(url('finance/selling_home'))->with('status', ' Deleted buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage selling range function----------------------------------------------------
        public function selling_range(Request $request)
        {
			//get request
			$start		= $request->dateStart;
			$end   		= $request->dateEnd;
			$id				= $request->idadd;
			$nota			= $request->nota;
			$cust_id	= $request->cust_id;

			// validate empty
			if($start == "" && $end == "" && $id == "" &&  $nota == "" &&  $cust_id == ""){
					return redirect(url('finance/selling_home'));

			}else{
				// validate return date if null
				if($start != ""){
						//convert date
						$date_start = strtotime($start);
						$date_start_format = date("Y-m-d",$date_start);
						$date_end = strtotime($end);
						$date_end_format = date("Y-m-d",$date_end);
				}else{
						$date_start_format = $start;
						$date_end_format = $end;
				}

				$date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
				$nota_sc 		= $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
				$id_sc			= $id != "" ? "AND a.penjualan_id LIKE '%".$id."%'" : "";
				$cust_id_sc = $cust_id != "" ? "AND a.customer_id LIKE '%".$cust_id."%'" : "";

				$sellings = DB::select("SELECT a.*, b.status_piutang AS 'status_piutang', c.customer_id AS 'custid', c.nama AS 'namacustomer'
												FROM ms_penjualan a LEFT JOIN lt_piutang b
												ON a.id = b.penjualan_id
												LEFT JOIN ms_customer c
												ON a.customer_id = c.customer_id
												WHERE a.status = 'A'
												$date_sc $nota_sc $id_sc $cust_id_sc ");


				$arraydata = [$date_start_format,$date_end_format,$id,$nota,$cust_id];

				// return to view
				return view('finance/selling/sellinghome')->with('sellings', $sellings)->with('arraydata', $arraydata);
			}
        }
        // ---------------------------------------------------------------------------------

        // manage selling export function---------------------------------------------------
        public function index_selling_export(Request $request)
        {
            //get request
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id             = $request->idadd;
            $nota           = $request->nota;
            $cust_id    = $request->cust_id;

            if($start != ""){
                //convert date
                $date_start = strtotime($start);
                $date_start_format = date("Y-m-d",$date_start);
                $date_end = strtotime($end);
                $date_end_format = date("Y-m-d",$date_end);
            }else{
                $date_start_format = $start;
                $date_end_format = $end;
            }


                $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
                $nota_sc        = $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
                $id_sc          = $id != "" ? "AND a.penjualan_id LIKE '%".$id."%'" : "";
                $cust_id_sc = $cust_id != "" ? "AND a.customer_id LIKE '%".$cust_id."%'" : "";

                $sellings = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                                b.status_piutang AS 'status_piutang',
                                c.customer_id AS 'custid', c.nama AS 'namacustomer'
                                FROM ms_penjualan a LEFT JOIN lt_piutang b
                                ON a.id = b.penjualan_id
                                LEFT JOIN ms_customer c
                                ON a.customer_id = c.customer_id
                                WHERE a.status = 'A'
                                $date_sc $nota_sc $id_sc $cust_id_sc ");

                $arraydata = [$date_start_format,$date_end_format,$id,$nota,$cust_id];

                Excel::create('Data Selling : '.date("d-m-Y").'', function($result) use($sellings,$date_start_format,$date_end_format,$arraydata)
                {
                    $result->sheet('Data Selling', function($sheet) use($sellings,$date_start_format,$date_end_format,$arraydata)
                    {
                        $i = 1;
                        $count = 0;
                        foreach($sellings as $item){
                                $i++;
                                $count++;
                                $item->no_nota == "" ? $customer = '' : $customer = $item->custid.'-'.$item->namacustomer;
                                if($item->status_piutang){
                                    $item->status_piutang == 1 ? $statuspiut = 'UNPAID' : $statuspiut = 'PAID';
                                } else {
                                    $statuspiut = 'PAID';
                                }
                                $data=[];
                                array_push($data, array(
                                        $item->createddate,
                                        $item->penjualan_id,
                                        $item->tanggal,
                                        $item->no_nota,
                                        $customer,
                                        $statuspiut,
                                        $item->tanggal_jatuh_tempo_penjualan,
                                        $item->penjualan_dp,
                                        $item->penjualan_bayar,
                                        $item->penjualan_total
                                ));
                            $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SELLING REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }
                    $sheet->row(9, array('Created Date', 'Selling ID', 'Date Selling', 'Nota', 'Customer ID', 'Status', 'Due Date', 'Down Payment', 'Paid', 'Total Price'));

                    $sheet->setBorder('A9:J9', 'thin');

                    // set style column
                    $sheet->cells('A9:J9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:J1');
                    $sheet->cells('A1:J1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':J'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);


                });
            })->download('xls');

            // return to view
            return view('finance/selling/sellinghome', ['sellings' => $sellings, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage selling------------------------------------------------------------------

    // menu manage credit-------------------------------------------------------------------

        // manage credit index function-----------------------------------------------------
        public function index_credit()
        {
    		$lt_piutang = DB::select("SELECT a.*, b.penjualan_id AS 'b_penjualan_id', b.tanggal_jatuh_tempo_penjualan AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
    										FROM lt_piutang a LEFT JOIN ms_penjualan b
    										ON a.penjualan_id = b.id
    										WHERE a.status = 'A'
    										AND a.status_piutang <> '2'");

    		$arraydata = ['','','','','all'];

            // return to view
            return view('finance/credit/credithome')->with('lt_piutang', $lt_piutang)->with('arraydata', $arraydata);
        }
        // ---------------------------------------------------------------------------------

        // manage selling edit function-----------------------------------------------------
        public function edit_credit($id)
        {
    		$lt_piutang = manage_credit::find($id);
            return view('finance/credit/creditedit', ['lt_piutang' => $lt_piutang]);
        }
        // ---------------------------------------------------------------------------------

        // manage selling do edit function--------------------------------------------------
        public function do_edit_credit(Request $request)
        {
    		$credit_id = $request->credit_id;
    		$mustpaid = $request->mustpaid;

            $this->validate($request, [
                'pay'             => 'required|min:1|max:20'
            ]);

    		if($mustpaid<$request->pay){
    			return redirect(url('finance/credit_home/edit/'.$credit_id))->with('error', ' Pay can not more than must paid .');
    		}

    		$piutang = manage_credit::find($credit_id);
    		$piutang->bayar = $piutang->bayar + $request->pay;
    		if($piutang->bayar==$piutang->total){
    			$piutang->status_piutang = 0;
    		}
    		$piutang->save();

    		$penjualan = manage_selling::find($piutang->penjualan_id);
    		$data = deposit_cust::where('customer_id', $penjualan->customer_id)->first();
    		$datafix = deposit_cust::find($data->id);
    		$datafix->deposit                        	= $datafix->deposit-$request->pay;
    		$datafix->last_modify_date                 = date('Y-m-d H:i:s');
    		$datafix->modify_user_id                   = Auth::user()->karyawan_id;
    		$datafix->save();

    		$credithistory = new manage_credit_history;
    		$credithistory->piutang_id 								= $piutang->id;
    		$credithistory->total_piutang	 						= $piutang->total;
    		$credithistory->total_pembayaran_piutang 	= $request->pay;
    		$credithistory->created_date              = date('Y-m-d H:i:s');
    		$credithistory->last_modify_date          = date('Y-m-d H:i:s');
    		$credithistory->modify_user_id            = Auth::user()->karyawan_id;
    		$credithistory->status                  	= 'A';
    		$credithistory->save();
            return redirect(url('finance/credit_home'))->with('status', ' Updated credit has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage selling range function----------------------------------------------------
        public function credit_range(Request $request)
        {
    		//get request
    		$start			= $request->dateStart;
    		$end   			= $request->dateEnd;
    		$idselling	= $request->idselling;
    		$idcredit		= $request->idcredit;
    		$status			= $request->status;

    		// validate empty
    		if($start == "" && $end == "" && $idselling == "" &&  $idcredit == "" &&  $status == ""){
    				return redirect(url('finance/credit_home'));
    		}else{
        		// validate return date if null
        		if($start != ""){
        				//convert date
        				$date_start = strtotime($start);
        				$date_start_format = date("Y-m-d",$date_start);
        				$date_end = strtotime($end);
        				$date_end_format = date("Y-m-d",$date_end);
        		}else{
        				$date_start_format = $start;
        				$date_end_format = $end;
        		}


        		$date_sc    			= $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
        		$idselling_sc 		= $idselling != "" ? "AND b.penjualan_id LIKE '%".$idselling."%'" : "";
        		$idcredit_sc			= $idcredit != "" ? "AND a.piutang_id LIKE '%".$idcredit."%'" : "";
        		if($status=="unpaid"){
        			$status_sc = $status != "" ? "AND a.status_piutang ='1'" : "";
        		} else if($status=="paid"){
        			$status_sc = $status != "" ? "AND a.status_piutang ='0'" : "";
        		} else if($status=="all"){
        			$status_sc = $status != "" ? "AND a.status_piutang <>'2'" : "";
        		}


        		$lt_piutang = DB::select("SELECT a.*, b.penjualan_id AS 'b_penjualan_id', b.tanggal_jatuh_tempo_penjualan AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
        										FROM lt_piutang a LEFT JOIN ms_penjualan b
        										ON a.penjualan_id = b.id
        										WHERE a.status = 'A'
        										$date_sc $idselling_sc $idcredit_sc $status_sc ");


        		$arraydata = [$date_start_format,$date_end_format,$idselling,$idcredit,$status];

                  // return to view
                return view('finance/credit/credithome')->with('lt_piutang', $lt_piutang)->with('arraydata', $arraydata);
			}
        }
        // ---------------------------------------------------------------------------------

        // manage credit export function----------------------------------------------------
        public function index_credit_export(Request $request)
        {
            //get request
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idselling  = $request->idselling;
            $idcredit       = $request->idcredit;
            $status         = $request->status;

            // validate return date if null
            if($start != ""){
                    //convert date
                    $date_start = strtotime($start);
                    $date_start_format = date("Y-m-d",$date_start);
                    $date_end = strtotime($end);
                    $date_end_format = date("Y-m-d",$date_end);
            }else{
                    $date_start_format = $start;
                    $date_end_format = $end;
            }

            $date_sc                = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $idselling_sc       = $idselling != "" ? "AND b.penjualan_id LIKE '%".$idselling."%'" : "";
            $idcredit_sc            = $idcredit != "" ? "AND a.piutang_id LIKE '%".$idcredit."%'" : "";
            if($status=="unpaid"){
                $status_sc = $status != "" ? "AND a.status_piutang ='1'" : "";
            } else if($status=="paid"){
                $status_sc = $status != "" ? "AND a.status_piutang ='0'" : "";
            } else if($status=="all"){
                $status_sc = $status != "" ? "AND a.status_piutang <>'2'" : "";
            }


            $lt_piutang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                            b.penjualan_id AS 'b_penjualan_id', b.tanggal_jatuh_tempo_penjualan AS 'jatuhtempo',
                            b.modify_user_id AS 'modify_user_id'
                            FROM lt_piutang a LEFT JOIN ms_penjualan b
                            ON a.penjualan_id = b.id
                            WHERE a.status = 'A'
                            $date_sc $idselling_sc $idcredit_sc $status_sc ");

            $arraydata = [$date_start_format,$date_end_format,$idselling,$idcredit,$status];

            Excel::create('Data Credit : '.date("d-m-Y").'', function($result) use($lt_piutang,$arraydata)
            {
              $result->sheet('Data Credit', function($sheet) use($lt_piutang,$arraydata)
              {
                $i = 1;
                    $count = 0;
                foreach($lt_piutang as $item){
                    $i++;
                    $count++;
                    $item->status_piutang > 0 ? $statuspiut = 'UNPAID' : $statuspiut = 'PAID';
                    $data=[];
                    array_push($data, array(
                        $item->createddate,
                        $item->piutang_id,
                        $item->b_penjualan_id,
                        $item->total,
                        $item->bayar,
                        $statuspiut
                    ));
                    $sheet->fromArray($data, null, 'A10', false, false);
                }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','CREDIT REPORT'));
                        $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                      $sheet->row(7, array('Date Start : ','ALL RANGE'));
                      $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                      $sheet->row(7, array('Date Start : ',$arraydata[0]));
                      $sheet->row(8, array('Date End :',$arraydata[1]));
                    }
                    $sheet->row(9, array('Created Date','Credit ID','Selling ID','Total','Paid','Status Credit'));


                    $sheet->setBorder('A9:F9', 'thin');

                    // set style column


                    $sheet->cells('A9:F9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:F1');
                    $sheet->cells('A1:F1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                for($k=9;$k<=$i+8;$k++){
                  $sheet->setBorder('A'.$k.':F'.$k, 'thin');
                }
                for($k=1;$k<=$i+9;$k++){
                  $sheet->setHeight(array
                  (
                    $k => '20',
                  ));
                }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
              });
            })->download('xls');

            // return to view
            return view('finance/credit/credithome', ['lt_piutang' => $lt_piutang, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage credit-------------------------------------------------------------------

    // menu selling return------------------------------------------------------------------

        // selling return index function----------------------------------------------------
        public function index_salreturn()
        {
            // query left join
            $tr_returpenjualan = DB::select("SELECT a.*, b.penjualan_id AS 'b_penjualan_id', SUM(c.qty) AS 'jumlahqty'
            FROM ms_returpenjualan a LEFT JOIN ms_penjualan b
            ON a.penjualan_id = b.id
            LEFT JOIN tr_returpenjualan c
            ON a.id = c.returpenjualan_id
            WHERE a.status = 'A'
            GROUP BY a.id");

            // return to view
            return view('finance/sales/salreturnhome', ['tr_returpenjualan' => $tr_returpenjualan]);
        }
        // ---------------------------------------------------------------------------------

        // selling return seelct2 selling function------------------------------------------
        public function search_selling()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_penjualan = DB::table('ms_penjualan')
                        ->select('ms_penjualan.*','tr_detail_penjualan.barang_id AS items_id','tr_detail_penjualan.qty AS qty','tr_detail_penjualan.sub_total_penjualan AS sub_total')
                        ->leftJoin('tr_detail_penjualan', 'ms_penjualan.penjualan_id', '=', 'tr_detail_penjualan.detail_penjualan_id')
                        ->leftJoin('ms_barang', 'tr_detail_penjualan.barang_id', '=', 'ms_barang.barang_id')
                        ->where('ms_penjualan.status',"A")
												->whereNotNull('ms_penjualan.no_nota')
                        ->where('ms_penjualan.penjualan_id','like', "%".$term."%" )
                        ->orWhere('ms_penjualan.no_nota','like', "%".$term."%" )
                        ->orWhere('ms_penjualan.created_date','like', "%".$term."%" )
                        ->get();

            $query = $ms_penjualan;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->penjualan_id));
										$new_row['text']=htmlentities(stripslashes($row->penjualan_id.' - '.$row->no_nota.' - '.date('d/m/Y', strtotime($row->created_date))));
                    $new_row['items_id']=htmlentities(stripslashes($row->items_id));
                    $new_row['qty']=htmlentities(stripslashes($row->qty));
                    $new_row['sub_total']=htmlentities(stripslashes($row->sub_total));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // selling return add function------------------------------------------------------
        public function add_salreturn()
        {
    		$return = manage_returnpenjualan::all();
    		if($return->isEmpty()){
    			$idreturn = "RS00000001";
    		} else {
    			//buat id
    			$returnn = manage_returnpenjualan::orderBy('id', 'DESC')->first();
    			$lastnumber = substr($returnn->retur_id, 2, 8);
    			$idnumber = $lastnumber + 1;

    			$idreturn = "RS".sprintf("%08d", $idnumber);
    			//buat id
    		}

           return view('finance/sales/salreturnadd')->with('idreturn', $idreturn);
        }
        // ---------------------------------------------------------------------------------

        // selling return do add function---------------------------------------------------
        public function do_add_salreturn(Request $request)
        {
    		$return = manage_returnpenjualan::all();
    		if($return->isEmpty()){
    			$idreturn = "RS00000001";
    		} else {
    			//buat id
    			$returnn = manage_returnpenjualan::orderBy('id', 'DESC')->first();
    			$lastnumber = substr($returnn->retur_id, 2, 8);
    			$idnumber = $lastnumber + 1;

    			$idreturn = "RS".sprintf("%08d", $idnumber);
    			//buat id
    		}

    		$penjualan = manage_selling::where('penjualan_id', $request->buy_id)->first();
    		$retur = new manage_returnpenjualan;
    		$retur->retur_id = $idreturn;
    		$retur->created_date = date('Y-m-d H:i:s');
    		$retur->penjualan_id = $penjualan->id;
    		$retur->status = 'A';
    		$retur->save();

    		return redirect(url('finance/salreturn_home/addnext/'.$retur->id));
        }
        // ---------------------------------------------------------------------------------

        // selling return add next get function---------------------------------------------
    	public function salnext_return_get($id)
    	{
    		$detailpenjualan = manage_selling_history::find($id);

    		if($detailpenjualan->status=="T"){
    			$barangtemp = manage_items_temp::find($detailpenjualan->barang_id);
    			$data[] = array(
                'iditemsprimary' => $detailpenjualan->barang_id,
                'items' => $barangtemp->nama,
                'subtotal' => $barangtemp->harga_jual,
                'qtyasli' => $detailpenjualan->qty,
                'qtysudahreturn' => $detailpenjualan->qty_return,
                'stockbarang' => $barangtemp->qty,
            );
    		} else {
    			if($detailpenjualan->type_sell==1){
    				$barang = manage_service::find($detailpenjualan->barang_id);
    				$data[] = array(
    	            'iditemsprimary' => $detailpenjualan->barang_id,
    	            'items' => $barang->name,
    	            'subtotal' => $barang->price,
    	            'qtyasli' => $detailpenjualan->qty,
    	            'qtysudahreturn' => $detailpenjualan->qty_return,
    	            'stockbarang' => $barang->qty,
    	        );
    			} else {
    				$barang = manage_items::find($detailpenjualan->barang_id);
    				$data[] = array(
    	            'iditemsprimary' => $detailpenjualan->barang_id,
    	            'items' => $barang->nama,
    	            'subtotal' => $barang->harga_jual,
    	            'qtyasli' => $detailpenjualan->qty,
    	            'stockbarang' => $barang->stock,
    	        );
    			}

    		}
            return json_encode($data);
    	}
        // ---------------------------------------------------------------------------------

        // selling return do next function--------------------------------------------------
    	public function saldo_addnext_salreturn(Request $request)
        {
    		$last_qty = $request->items_qty_view;

            if($request->items_qty_view > ($request->qtyasli-$request->qtysudahreturn)){
                return redirect(url('finance/salreturn_home/addnext/'.$request->returid))->with('error', ' Quantities return must be less than last quantities or same.');
            }

    				$retur = manage_returnpenjualan::find($request->returid);
    				$retur->total_return = $retur->total_return + $request->items_grand_total_view;
    				$retur->save();

    				$penjualan = manage_selling::find($retur->penjualan_id);
    				$deposittt = deposit_cust::where('customer_id', $penjualan->customer_id)->first();
    				if($request->type_return==1){
    						if($deposittt->deposit==0){
    							return redirect(url('finance/salreturn_home/addnext/'.$request->returid))->with('error', ' Can not use CUT THE DEBT as Type Return because Deposit is 0.');
    						}
    				}

    				$penjualan_detail = manage_selling_history::find($request->detailpenjualanid);

            $data = new manage_sales_return;
            $data->returpenjualan_id           	= $request->returid;
            $data->barang_id                    = $request->iditemsprimary;
            $data->qty                          = $request->items_qty_view;
            $data->type_return                  = $request->type_return;
            $data->sub_total                 		= $request->subtotal;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
    				if($penjualan_detail->status=="T"){
    					$data->status                       = 'T';
    				} else {
    					$data->status                       = 'A';
    				}
    				$data->type_sell 										= $penjualan_detail->type_sell;
            $data->save();

    				$penjualan_detail->qty_return = $penjualan_detail->qty_return + $request->items_qty_view;
    				$penjualan_detail->save();
						if($request->type_return!=3){
							if($penjualan_detail->status=="T"){
	    					$barang = manage_items_temp::find($request->iditemsprimary);
	    					$barang->qty = $request->stockbarang + $last_qty;
	    					$barang->save();
	    				} else {
	    					if($penjualan_detail->type_sell==1){
	    						$barang = manage_service::find($request->iditemsprimary);
	    						$barang->qty = $request->stockbarang + $last_qty;
	    						$barang->save();
	    					} else {
	    						$barang = manage_items::find($request->iditemsprimary);
	    						$barang->stock = $request->stockbarang + $last_qty;
	    						$barang->save();
	    					}
	    				}
						}

    				if($request->type_return==1 && $deposittt->deposit!=0){
    					return redirect(url('finance/salreturn_home/addnext/cdb/'.$data->id.'/'.$request->returid.'/'.$request->items_grand_total_view));
    				} else if($request->type_return==4){
							return redirect(url('finance/selling_home/tig/add/'.$request->items_grand_total_view.'/'.$request->returid.'/'.$data->id));
						}

            return redirect(url('finance/salreturn_home/addnext/'.$request->returid))->with('status', ' Created new purchase return has been success.');
        }
        // ---------------------------------------------------------------------------------

    	// selling return add next cut the debt function------------------------------------
    	public function saladdnext_cdb($detailid,$id,$total)
    	{
    		//mencari supplier_id
    		$retur = manage_returnpenjualan::find($id);
    		$penjualan = manage_selling::find($retur->penjualan_id);
    		$customer_id = $penjualan->customer_id;
    		//mencari supplier_id


    		$lt_piutang = DB::table('lt_piutang')
    								->select('lt_piutang.*', 'ms_penjualan.customer_id as customer_id')
    								->leftJoin('ms_penjualan', 'lt_piutang.penjualan_id', 'ms_penjualan.id')
    								->where('lt_piutang.status', 'A')
    								->where('lt_piutang.status_piutang', '<>','0')
    								->where('lt_piutang.status_piutang', '<>', '2')
    								->get();

    		return view('finance/sales/salreturnadd_next_cdb')->with('lt_piutang', $lt_piutang)->with('customer_id', $customer_id)->with('retur_id', $id)->with('total', $total)->with('detailid', $detailid);
    	}
    	// ---------------------------------------------------------------------------------

    	// selling return do next cut the debt function-------------------------------------
    	public function saldo_addnext_cdb(Request $request)
    	{
    		$total_return = $request->total_return;
    		if(null == Input::get('cbpilih')){
    			return redirect(url('finance/salreturn_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
    		} else {
    			foreach (Input::get('cbpilih') as $key => $piutangid) {
    				$piutang = manage_credit::find($key);
    				$lunas = $piutang->total-$piutang->bayar;
    				if($lunas <= $total_return){
    					$piutang->bayar = $piutang->total;
    					$total_return = $total_return - $lunas;
    					$piutang->status_piutang = 0;
    					$piutang->save();

							$credithistory = new manage_credit_history;
		    			$credithistory->piutang_id 								= $piutang->id;
		    			$credithistory->total_piutang	 						= $piutang->total;
		    			$credithistory->total_pembayaran_piutang 	= $lunas;
		    			$credithistory->created_date              = date('Y-m-d H:i:s');
		    			$credithistory->last_modify_date          = date('Y-m-d H:i:s');
		    			$credithistory->modify_user_id            = Auth::user()->karyawan_id;
		    			$credithistory->status                  	= 'A';
		    			$credithistory->salreturndetail_id       	= $request->detailid;
		    			$credithistory->save();

    				} else if($total_return==0){

    				} else {
    					$piutang->bayar = $piutang->bayar + $total_return;
    					$piutang->save();

							$credithistory = new manage_credit_history;
		    			$credithistory->piutang_id 								= $piutang->id;
		    			$credithistory->total_piutang	 						= $piutang->total;
		    			$credithistory->total_pembayaran_piutang 	= $total_return;
		    			$credithistory->created_date              = date('Y-m-d H:i:s');
		    			$credithistory->last_modify_date          = date('Y-m-d H:i:s');
		    			$credithistory->modify_user_id            = Auth::user()->karyawan_id;
		    			$credithistory->status                  	= 'A';
							$credithistory->salreturndetail_id       	= $request->detailid;
		    			$credithistory->save();

							$total_return = 0;

    				}
    			}

    			$total_piutang = 0;
    			foreach (Input::get('cbpilih') as $key => $piutangid) {
    				$piutang = manage_credit::find($key);
    				$total_piutang += $piutang->total - $piutang->bayar;
    			}

    			$data = deposit_cust::where('customer_id', $request->customer_id)->first();
    			$datafix = deposit_cust::find($data->id);
    			$datafix->deposit                        	= $total_piutang;
    			$datafix->last_modify_date                 = date('Y-m-d H:i:s');
    			$datafix->modify_user_id                   = Auth::user()->karyawan_id;
    			$datafix->save();

    			return redirect(url('finance/salreturn_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
    		}
    	}
    	// ---------------------------------------------------------------------------------

    	// selling return next function-----------------------------------------------------
        public function salnext_return($id)
        {
    		$return = manage_returnpenjualan::find($id);
    		$penjualan = manage_selling::find($return->penjualan_id);

    		return view('finance/sales/salreturnadd_next')->with('return',$return)->with('penjualan',$penjualan);
    	}
        // ---------------------------------------------------------------------------------

        // selling return edit function-----------------------------------------------------
        public function edit_salreturn($id)
        {
            // query left join
            $tr_returpenjualan = DB::table('tr_returpenjualan')
                            ->select('tr_returpenjualan.*','ms_penjualan.penjualan_total AS total_payment','ms_penjualan.tanggal AS payment_date','tr_detail_penjualan.qty AS quantities','tr_detail_penjualan.sub_total_penjualan AS sub_price','tr_detail_penjualan.stock_id AS stock_id', 'tr_detail_penjualan.items_id AS items_id')
                            ->leftJoin('ms_penjualan', 'tr_returpenjualan.penjualan_id', '=', 'ms_penjualan.penjualan_id')
                            ->leftJoin('tr_detail_penjualan', 'tr_returpenjualan.penjualan_id', '=', 'tr_detail_penjualan.detail_penjualan_id')
                            ->where('tr_returpenjualan.status', 'A')
                        ->where('tr_returpenjualan.id', $id)
                        ->first();

            return view('finance/sales/salreturnedit', ['tr_returpenjualan' => $tr_returpenjualan]);
        }
        // ---------------------------------------------------------------------------------

        // selling return do edit function--------------------------------------------------
        public function do_edit_salreturn(Request $request, $id)
        {
            $this->validate($request, [
                'return_id'             => 'required',
                'type_return'           => 'required'
            ]);

            $data = manage_sales_return::find($id);
            $data->retur_penjualan_id               = $request->return_id;
            $data->type_return                      = $request->type_return;

            $data->last_modify_date                 = date('Y-m-d H:i:s');
            $data->modify_user_id                   = Auth::user()->karyawan_id;
            $data->status                           = 'A';
            $data->save();

            return redirect(url('finance/salreturn_home'))->with('status', ' Updated sales return has been success.');
        }
        // ---------------------------------------------------------------------------------

        // selling return delete function---------------------------------------------------
        public function delete_salreturn($id)
        {
            $data = manage_returnpenjualan::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            //$data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            return redirect(url('finance/salreturn_home'))->with('status', ' Deleted sales return has been success.');
        }
        // ---------------------------------------------------------------------------------

        // selling return range function----------------------------------------------------
        public function salreturn_range(Request $request)
        {
			//get tanggal
			$start 		= $request->dateStart;
			$end   		= $request->dateEnd;
			$idreturn	= $request->idreturn;
			$idselling	= $request->idselling;
			$total		= $request->total ;

			// validate empty
			if($start == "" && $end == "" && $idreturn == "" &&  $idselling == "" &&  $total == ""){
					return redirect(url('finance/salreturn_home'));
			}else{
				// validate return date if null
				if($start != ""){
						//convert date
						$date_start = strtotime($start);
						$date_start_format = date("Y-m-d",$date_start);
						$date_end = strtotime($end);
						$date_end_format = date("Y-m-d",$date_end);
				}else{
						$date_start_format = $start;
						$date_end_format = $end;
				}

				$date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
				$idreturn_sc = $idreturn != "" ? "AND a.retur_id LIKE '%".$idreturn."%'" : "";
				$idselling_sc = $idselling != "" ? "AND b.penjualan_id LIKE '%".$idselling."%'" : "";
				$total_sc = $total != "" ? "AND a.total_return LIKE '%".$total."%'" : "";

				$tr_returpenjualan = DB::select("SELECT a.*, b.penjualan_id AS 'b_penjualan_id', SUM(c.qty) AS 'jumlahqty'
								FROM ms_returpenjualan a LEFT JOIN ms_penjualan b
								ON a.penjualan_id = b.id
								LEFT JOIN tr_returpenjualan c
								ON a.id = c.returpenjualan_id
								WHERE a.status = 'A'
								$date_sc $idreturn_sc $idselling_sc $total_sc
								GROUP BY a.id");

                $arraydata = [$date_start_format,$date_end_format,$idreturn,$idselling,$total];
                // return to view
                return view('finance/sales/salreturnhome')->with('tr_returpenjualan',$tr_returpenjualan)->with('arraydata',$arraydata);
            } 
        }
        // ---------------------------------------------------------------------------------

        // selling return export function------------------------------------------------------
        public function index_salreturn_export(Request $request)
        {
            //get tanggal
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idreturn       = $request->idreturn;
            $idselling  = $request->idselling;
            $total          = $request->total;

            // validate return date if null
            if($start != ""){
                //convert date
                $date_start = strtotime($start);
                $date_start_format = date("Y-m-d",$date_start);
                $date_end = strtotime($end);
                $date_end_format = date("Y-m-d",$date_end);
            }else{
                $date_start_format = $start;
                $date_end_format = $end;
            }

            $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $idreturn_sc = $idreturn != "" ? "AND a.retur_id LIKE '%".$idreturn."%'" : "";
            $idselling_sc = $idselling != "" ? "AND b.penjualan_id LIKE '%".$idselling."%'" : "";
            $total_sc = $total != "" ? "AND a.total_return LIKE '%".$total."%'" : "";

            $tr_returpenjualan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                                b.penjualan_id AS 'b_penjualan_id', SUM(c.qty) AS 'jumlahqty'
                                FROM ms_returpenjualan a LEFT JOIN ms_penjualan b
                                ON a.penjualan_id = b.id
                                LEFT JOIN tr_returpenjualan c
                                ON a.id = c.returpenjualan_id
                                WHERE a.status = 'A'
                                $date_sc $idreturn_sc $idselling_sc $total_sc
                                GROUP BY a.id");

            $arraydata = [$date_start_format,$date_end_format,$idreturn,$idselling,$total];

            Excel::create('Data Sales Return : '.date("d-m-Y").'', function($result) use($tr_returpenjualan,$arraydata)
            {
                $result->sheet('Data Sales Return', function($sheet) use($tr_returpenjualan,$arraydata)
                {
                    $i = 1;
                    $count = 0;
                    foreach($tr_returpenjualan as $item){
                        $i++;
                        $count++;
                        $data=[];
                        array_push($data, array(
                            $item->createddate,
                            $item->retur_id,
                            $item->b_penjualan_id,
                            $item->jumlahqty,
                            $item->total_return,
                            $item->status
                        ));
                        $sheet->fromArray($data, null, 'A10', false, false);
                    }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SALES RETURN REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }
                    $sheet->row(9, array('ID', 'SELLING ID','Total QTY','Total','Status', 'Created Date'));


                    $sheet->setBorder('A9:F9', 'thin');

                    // set style column
                    $sheet->cells('A9:F9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:F1');
                    $sheet->cells('A1:F1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':F'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                });
            })->download('xls');

            // return to view
            return view('finance/sales/salreturnhome', ['tr_returpenjualan' => $tr_returpenjualan, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu selling return------------------------------------------------------------------

    // menu addtional cost------------------------------------------------------------------

        // addtional cost index function----------------------------------------------------
        public function index_addtional()
        {
            $lt_biayalain = DB::select("SELECT a.*, SUM(b.jumlah) AS 'totaljumlah_detail'
            				FROM lt_biayalain a LEFT JOIN lt_biayalain_detail b
            				ON a.id = b.biayalain_id
            				WHERE a.status = 'A'
            				GROUP BY a.id");

            return view('finance/addtional/addhome', ['lt_biayalain' => $lt_biayalain]);
        }
        // ---------------------------------------------------------------------------------

        // addtional cost add function------------------------------------------------------
        public function add_addtional()
        {
            $validasi = 0;
            return view('finance/addtional/costadd')->with('validasi', $validasi);
        }
        // ---------------------------------------------------------------------------------

        // addtional cost add next function-------------------------------------------------
        public function addnext_addtional($id)
        {
            $biayalain = manage_biayalain::find($id);
            $validasi = 1;

            //menghitung total------------------------------------------------------------------------
            $total = 0;
                $totalharga = 0;
            $details = manage_biayalain_detail::where('biayalain_id', $id)->where('status', 'A')->get();
            foreach ($details as $detail) {
                $total += $detail->jumlah;
                    $totalharga += $detail->jumlah*$detail->harga;
            }
            //menghitung total------------------------------------------------------------------------

            return view('finance/addtional/costadd_next')->with('biayalain', $biayalain)->with('validasi', $validasi)->with('total', $total)->with('totalharga', $totalharga);
        }
        // ---------------------------------------------------------------------------------

        // addtional cost do add function---------------------------------------------------
        public function do_add_addtional(Request $request)
        {
            // validasi untuk validate 1 kondisi
                $this->validate($request, [
                        'add_name'   => 'required|max:100',
                        'add_kat'    => 'max:100',
                        'add_total'  => 'required|max:10',
                        'add_mount'  => 'required|max:20'
                ]);
            // validasi untuk validate 1 kondisi

            //buying id validasi----------------------------------------------------------------------
            if($request->validasi==0){
            } else {
                $msid = $request->id_biayalain;
            }
            //buying id validasi----------------------------------------------------------------------

            // calculate total pembelian-------------------------------------------------------------
            $total_price = $request->add_total;
            // calculate total pembelian-------------------------------------------------------------


            // validasi quantities-------------------------------------------------------------------
            if($total_price == 0){
                if($request->validasi==0){
                    return redirect(url('finance/addtional_home/add'))->with('error', ' Total = 0, Please check the total value.');
                } else {
                    return redirect(url('finance/addtional_home/addnext/'.$msid))->with('error', ' Total = 0, Please check the total value.');
                }
            }
            // validasi quantities-------------------------------------------------------------------

            if($request->validasi==0){
                //buat id
                $biayalain = manage_biayalain::orderBy('id', 'DESC')->first();
                if($biayalain){
                    $lastnumber = substr($biayalain->biayalain_id, 3, 7);
                    $idnumber = $lastnumber + 1;

                    $biayalainid = "ADC".sprintf("%07d", $idnumber);
                } else {
                    $biayalainid = "ADC0000001";
                }

                //buat id
                // save to ms_pembelian------------------------------------------------------------------
                $data = new manage_biayalain;
                $data->biayalain_id                     = $biayalainid;
                $data->created_date                     = date('Y-m-d H:i:s');
                $data->last_modify_date                 = date('Y-m-d H:i:s');
                $data->modify_user_id                   = Auth::user()->karyawan_id;
                $data->status                           = 'A';
                $data->save();
            }
            // save to ms_pembelian-------------------------------------------------------------------

            //buying id validasi----------------------------------------------------------------------
            if($request->validasi==0){
                $msid = $data->id;
            } else {
                $msid = $request->id_biayalain;
            }
            //buying id validasi----------------------------------------------------------------------

            // save to tr_detail_pembelian------------------------------------------------------------
                $datadetail = new manage_biayalain_detail;
                $datadetail->biayalain_id                           = $msid;
                $datadetail->nama                                   = $request->add_name;
                $datadetail->kategori                         = $request->add_kat;
                $datadetail->jumlah                                         = $request->add_total;
                $datadetail->harga                                          = $request->add_mount;
                $datadetail->created_date                     = date('Y-m-d H:i:s');
                $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
                $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
                $datadetail->status                           = 'A';
                $datadetail->save();
            // save to tr_detail_pembelian------------------------------------------------------------

            return redirect(url('finance/addtional_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // addtional cost edit function-----------------------------------------------------
        public function edit_addtional($id)
        {
            $lt_biayalain = manage_biayalain::where('status', 'A')
                        ->where('id', $id)
                        ->first();

            return view('finance/addtional/costedit', ['lt_biayalain' => $lt_biayalain]);
        }
        // ---------------------------------------------------------------------------------

        // addtional cost do edit function--------------------------------------------------
        public function do_edit_addtional(Request $request, $id)
        {
            $this->validate($request, [
                'no_nota'     => 'required|max:100'
            ]);

            $data = manage_biayalain::find($id);
            $data->no_nota                  = $request->no_nota;
            $data->last_modify_date         = date('Y-m-d H:i:s');
            $data->modify_user_id           = Auth::user()->karyawan_id;
            $data->status                   = 'A';
            $data->save();

            return redirect(url('finance/addtional_home'))->with('status', ' Updated Addtional Cost has been success.');
        }
        // ---------------------------------------------------------------------------------

        // addtional cost delete function---------------------------------------------------
        public function delete_addtional($id)
        {
            $data = manage_biayalain::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            return redirect(url('finance/addtional_home'))->with('status', ' Deleted Addtional Cost has been success.');
        }
        // ---------------------------------------------------------------------------------

        // addtional cost detail function---------------------------------------------------
        public function delete_addtional_detail($detailid,$biayalainid)
        {
            $detailbiayalain = manage_biayalain_detail::find($detailid);
            $detailbiayalain->modify_user_id   = Auth::user()->karyawan_id;
            $detailbiayalain->status = "D";
            $detailbiayalain->deleted_date = date('Y-m-d H:i:s');
            $detailbiayalain->save();

            return redirect(url('finance/addtional_home/addnext/'.$biayalainid))->with('status', ' Deleted buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // addtional cost checkout function-------------------------------------------------
        public function index_addtional_checkout($id)
        {
            $biayalain = manage_biayalain::find($id);

            //menghitung total------------------------------------------------------------------------
            $total = 0;
                $totalbiaya = 0;
            $details = manage_biayalain_detail::where('biayalain_id', $id)->get();
            foreach ($details as $detail) {
                $total += $detail->jumlah;
                    $totalbiaya += $detail->jumlah*$detail->harga;
            }
            //menghitung total------------------------------------------------------------------------

            return view('finance/addtional/costadd_checkout')->with('total', $total)
                                                                                        ->with('biayalain', $biayalain)
                                                                                        ->with('totalbiaya', $totalbiaya);
        }
        // ---------------------------------------------------------------------------------

        // selling do checkout function
        public function index_addtional_docheckout(Request $request)
        {
            if($request->type_addtional != 1){
                $this->validate($request, [
                    'user_id'           => 'required|min:1|max:20',
                    'type_addtional'    => 'required|min:1',
                    'due_date'          => 'required'
                ]);
            }else{
                $this->validate($request, [
                    'user_id'           => 'required|min:1',
                    'type_addtional'    => 'required|min:1'
                ]);
            }

            $biayalain = manage_biayalain::find($request->biayalainid);
                $totalbiaya = 0;
                $details = manage_biayalain_detail::where('biayalain_id', $biayalain->id)->get();
                foreach ($details as $detail) {
                    $totalbiaya += $detail->harga*$detail->jumlah;
                }

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

                if($request->type_addtional==0){
                    $loan = new manage_loan;
                    $loan->loan_id = $id_loan;
                    $loan->user_id = $biayalain->biayalain_id;
                    $loan->total = $totalbiaya;
                    $loan->bayar = 0;
                    $loan->status_loan = 1;
                    $loan->loan_type = 0;
                    $loan->created_date = date('Y-m-d H:i:s');
                    $loan->last_modify_date = date('Y-m-d H:i:s');
                    $loan->modify_user_id = Auth::user()->karyawan_id;
                    $loan->tanggal_jatuh_tempo = $request->due_date;
                    $loan->status = 'A';
                    $loan->save();
                }


            // Edit to lt_biayalain------------------------------------------------------------------
                if($request->no_nota != ''){
                    $biayalain->no_nota                  = $request->no_nota;
                }else{
                    $biayalain->no_nota                  = 'NOTAUNSET_'.$request->user_id;
                }
                $biayalain->tanggal                      = $request->buy_date;
                $biayalain->total_biaya                  = $totalbiaya;
                $biayalain->type_cost                    = $request->type_addtional;
                $biayalain->user_id                      = $request->emp1_id;
                $biayalain->save();
            // Edit to lt_biayalain-------------------------------------------------------------------

            return redirect(url('finance/addtional_home'));
        }
        // ---------------------------------------------------------------------------------

        // addtional cost range function----------------------------------------------------
        public function addtional_range(Request $request)
        {
            //get tanggal
            $start 		= $request->dateStart;
            $end   		= $request->dateEnd;
						$id				= $request->idadd;
						$nota			= $request->nota;
						$amount			= $request->amount ;

						// validate empty
						if($start == "" && $end == "" && $id == "" &&  $nota == "" &&  $amount == ""){
								return redirect(url('finance/addtional_home'));
						}else{
							// validate return date if null
							if($start != ""){
									//convert date
									$date_start = strtotime($start);
									$date_start_format = date("Y-m-d",$date_start);
									$date_end = strtotime($end);
									$date_end_format = date("Y-m-d",$date_end);
							}else{
									$date_start_format = $start;
									$date_end_format = $end;
							}

							$date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
	            $nota_sc = $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
	            $id_sc = $id != "" ? "AND a.biayalain_id LIKE '%".$id."%'" : "";
	            $amount_sc = $amount != "" ? "AND a.total_biaya LIKE '%".$amount."%'" : "";

	            $lt_biayalain = DB::select("SELECT a.*, SUM(b.jumlah) AS 'totaljumlah_detail'
	                            FROM lt_biayalain a LEFT JOIN lt_biayalain_detail b
	                            ON a.id = b.biayalain_id
	                            WHERE a.status = 'A'
	                            $date_sc $nota_sc $id_sc $amount_sc
															GROUP BY a.id");

	            $arraydata = [$date_start_format,$date_end_format,$id,$nota,$amount];
            // return to view
            return view('finance/addtional/addhome')->with('lt_biayalain',$lt_biayalain)->with('arraydata',$arraydata);
					}
				}
        // ---------------------------------------------------------------------------------

        // addtional cost export function---------------------------------------------------
        public function export_addtional(Request $request)
        {
            //get tanggal
            $start 		= $request->dateStart;
            $end   		= $request->dateEnd;
            $id				= $request->idadd;
            $nota			= $request->nota;
            $amount			= $request->amount ;

            // validate return date if null
            if($start != ""){
            //convert date
            $date_start = strtotime($start);
            $date_start_format = date("Y-m-d",$date_start);
            $date_end = strtotime($end);
            $date_end_format = date("Y-m-d",$date_end);
            }else{
            $date_start_format = $start;
            $date_end_format = $end;
            }

            $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $nota_sc = $nota != "" ? "AND a.no_nota LIKE '%".$nota."%'" : "";
            $id_sc = $id != "" ? "AND a.biayalain_id LIKE '%".$id."%'" : "";
            $amount_sc = $amount != "" ? "AND a.total_biaya LIKE '%".$amount."%'" : "";

            $lt_biayalain = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                                SUM(b.jumlah) AS 'totaljumlah_detail'
            					FROM lt_biayalain a LEFT JOIN lt_biayalain_detail b
            					ON a.id = b.biayalain_id
            					WHERE a.status = 'A'
            					$date_sc $nota_sc $id_sc $amount_sc
            					GROUP BY a.id");

            $arraydata = [$date_start_format,$date_end_format,$id,$nota,$amount];

                Excel::create('Data Addtional : '.date("d-m-Y").'', function($result) use($lt_biayalain,$arraydata)
                {
                    $result->sheet('Data Addtional', function($sheet) use($lt_biayalain,$arraydata)
                {
                    $i = 1;
                    			$count = 0;
                    foreach($lt_biayalain as $item){
                    $i++;
    				$item->type_cost == '1' ? $typecost = 'Already' : $typecost = 'Not Yet';
    				$count++;
                    $data=[];
                    array_push($data, array(
                        $item->createddate,
                        $item->biayalain_id,
                        $item->no_nota,
                        $item->tanggal,
                        $item->totaljumlah_detail,
                        $item->total_biaya,
                        $typecost
                    ));
                    $sheet->fromArray($data, null, 'A10', false, false);
                }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('', 'ADDTIONAL REPORT'));
                    			$sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                    $sheet->row(7, array('Date Start : ','ALL RANGE'));
                    $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                    $sheet->row(7, array('Date Start : ',$arraydata[0]));
                    $sheet->row(8, array('Date End :',$arraydata[1]));
                    }

                    $sheet->row(9, array('Created Date','ID','Nota','Date','Total QTY','Total Price','Type Cost'));

    				$sheet->setBorder('A9:G9', 'thin');

    				// set style column
    				$sheet->cells('A9:G9', function($cells){
    					$cells->setFontSize('13');
    					$cells->setValignment('center');
    				});

    				// merge cell
    				$sheet->mergeCells('A1:G1');
    				$sheet->cells('A1:G1', function($cells){
    						$cells->setFontSize('15');
    						$cells->setAlignment('center');
    				});

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':G'.$k, 'thin');
                    }

                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                    	$cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                    	$cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                    	$cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                });

            })->download('xls');

            // return to view
            return view('finance/addtional/addhome')->with('lt_biayalain',$lt_biayalain)->with('arraydata',$arraydata);
        }
        // ---------------------------------------------------------------------------------

    // menu addtional cost------------------------------------------------------------------

    // menu manage loan --------------------------------------------------------------------

        // manage loan index function-------------------------------------------------------
        public function index_loan()
        {
            $lt_loan = DB::table('lt_loan')
                        ->select('lt_loan.*','ms_karyawan.nama AS employee_name','ms_customer.nama AS customer_name',
                            'ms_supplier.nama AS supplier_name')
                        ->leftJoin('ms_karyawan', 'lt_loan.user_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('ms_customer', 'lt_loan.user_id', '=', 'ms_customer.customer_id')
                        ->leftJoin('ms_supplier', 'lt_loan.user_id', '=', 'ms_supplier.supplier_id')
                        ->leftJoin('lt_biayalain', 'lt_loan.user_id', '=', 'lt_biayalain.biayalain_id')
                        ->where('lt_loan.status_loan', '<>', 2)
                        ->where('lt_loan.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Loan';
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
            return view('finance/loan/loanhome', ['lt_loan' => $lt_loan]);
        }
        // ---------------------------------------------------------------------------------

        // loan get range function----------------------------------------------------------
        public function loan_range(Request $request)
        {
            //get request
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idloan         = $request->idloan;
            $iduser         = $request->iduser;
            $typeuser       = $request->typeuser;


            // validate empty
            if($start == "" && $end == "" && $idloan == "" &&  $iduser == "" &&  $typeuser == ""){
                    return redirect(url('finance/loan_home'));
            }else{
                // validate return date if null
                if($start != ""){
                        //convert date
                        $date_start = strtotime($start);
                        $date_start_format = date("Y-m-d",$date_start);
                        $date_end = strtotime($end);
                        $date_end_format = date("Y-m-d",$date_end);
                }else{
                        $date_start_format = $start;
                        $date_end_format = $end;
                }


                $date_sc            = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
                $idloan_sc          = $idloan != "" ? "AND a.loan_id LIKE '%".$idloan."%'" : "";
                if($typeuser=="employee"){
                    $iduser_sc = $iduser != "" ? "AND d.karyawan_id LIKE '%".$iduser."%'" : "";
                } else if($typeuser=="customer"){
                    $iduser_sc = $iduser != "" ? "AND b.customer_id LIKE '%".$iduser."%'" : "";
                } else if($typeuser=="supplier"){
                    $iduser_sc = $iduser != "" ? "AND c.supplier_id LIKE '%".$iduser."%'" : "";
                } else {
                    $iduser_sc = $iduser != "" ? "AND e.biayalain_id LIKE '%".$iduser."%'" : "";
                }



                $lt_loan = DB::select("SELECT a.*, d.nama AS 'employee_name', b.nama AS 'customer_name', c.nama AS 'supplier_nama'
                                                FROM lt_loan a LEFT JOIN ms_customer b
                                                ON a.user_id = b.customer_id
                                                LEFT JOIN ms_supplier c
                                                ON a.user_id = c.supplier_id
                                                LEFT JOIN ms_karyawan d
                                                ON a.user_id = d.karyawan_id
                                                LEFT JOIN lt_biayalain e
                                                ON a.user_id = e.biayalain_id
                                                WHERE a.status = 'A'
                                                AND a.status_loan <> '2'
                                                $date_sc $idloan_sc $iduser_sc");


                $arraydata = [$date_start_format,$date_end_format,$idloan,$typeuser,$iduser];

                // return to view
                return view('finance/loan/loanhome')->with('lt_loan', $lt_loan)->with('arraydata', $arraydata);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage car select2 customer/employee/supplier function---------------------------
        public function search_loan_user()
        {
            $term = strip_tags(trim($_GET['q']));
            $typeterm = strip_tags(trim($_GET['j']));

            if($typeterm == 1){
                $ms_karyawan = DB::table('ms_karyawan')
                                ->select('ms_karyawan.*','lt_loan.total AS loan_total', 'lt_loan.id AS loan_id')
                                ->leftJoin('lt_loan', 'ms_karyawan.karyawan_id', '=', 'lt_loan.user_id')
                                ->where('ms_karyawan.status',"A")
                                ->where('ms_karyawan.nama','like', "%".$term."%" )
                                ->orWhere('ms_karyawan.karyawan_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_karyawan;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->karyawan_id));
                        $new_row['text']=htmlentities(stripslashes($row->karyawan_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $new_row['total']=htmlentities(stripslashes($row->loan_total));
                        $new_row['loan_id']=htmlentities(stripslashes($row->loan_id));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }

            }else if($typeterm == 2){
                $ms_customer = DB::table('ms_customer')
                                ->select('ms_customer.*','lt_loan.total AS loan_total', 'lt_loan.id AS loan_id')
                                ->leftJoin('lt_loan', 'ms_customer.customer_id', '=', 'lt_loan.user_id')
                                ->where('ms_customer.status',"A")
                                ->where('ms_customer.nama','like', "%".$term."%" )
                                ->orWhere('ms_customer.customer_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_customer;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->customer_id));
                        $new_row['text']=htmlentities(stripslashes($row->customer_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $new_row['total']=htmlentities(stripslashes($row->loan_total));
                        $new_row['loan_id']=htmlentities(stripslashes($row->loan_id));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }
            }else if($typeterm == 3){
                $ms_supplier = DB::table('ms_supplier')
                                ->select('ms_supplier.*','lt_loan.total AS loan_total', 'lt_loan.id AS loan_id')
                                ->leftJoin('lt_loan', 'ms_supplier.supplier_id', '=', 'lt_loan.user_id')
                                ->where('ms_supplier.status',"A")
                                ->where('ms_supplier.nama','like', "%".$term."%" )
                                ->orWhere('ms_supplier.supplier_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_supplier;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->supplier_id));
                        $new_row['text']=htmlentities(stripslashes($row->supplier_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $new_row['total']=htmlentities(stripslashes($row->loan_total));
                        $new_row['loan_id']=htmlentities(stripslashes($row->loan_id));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }
            }
        }
        // ---------------------------------------------------------------------------------

        // manage quar add function---------------------------------------------------------
        public function add_loan()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Loan';
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

            return view('finance/loan/loanadd');
        }
        // ---------------------------------------------------------------------------------

        // manage car do edit function------------------------------------------------------
        public function do_add_loan(Request $request)
        {
            $this->validate($request, [
                'type'              => 'required|min:1',
                'user_name'         => 'required',
                'total_loan'        => 'required|max:20',
                'due_date'          => 'required'
            ]);

            $id         = $request->user_id;
            $loan_id    = $request->loan_id;
            $calc       = $request->user_total + $request->total_loan;

            $data = manage_loan::where('user_id',$id)->update([
                'total' => $calc,
                'tanggal_jatuh_tempo' => $request->due_date,
                'status_loan' => 1,
                'last_modify_date' => date('Y-m-d H:i:s'),
                'modify_user_id' => Auth::user()->karyawan_id,
                'status' => 'A'
            ]);

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add Total Loan';
                $table_logs  = 'lt_loan';
                $id_logs     = $loan_id;
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

            return redirect(url('finance/loan_home'))->with('status', ' Add Total Loan has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage loan edit function--------------------------------------------------------
        public function edit_loan($id)
        {
            $lt_loan = DB::table('lt_loan')
                        ->select('lt_loan.*','ms_karyawan.nama AS employee_name','ms_customer.nama AS customer_name',
                            'ms_supplier.nama AS supplier_name')
                        ->leftJoin('ms_karyawan', 'lt_loan.user_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('ms_customer', 'lt_loan.user_id', '=', 'ms_customer.customer_id')
                        ->leftJoin('ms_supplier', 'lt_loan.user_id', '=', 'ms_supplier.supplier_id')
                        ->leftJoin('lt_biayalain', 'lt_loan.user_id', '=', 'lt_biayalain.biayalain_id')
                        ->where('lt_loan.status_loan', '<>', 2)
                        ->where('lt_loan.status', 'A')
                        ->where('lt_loan.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Loan';
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

            return view('finance/loan/loanedit', ['lt_loan' => $lt_loan]);
        }
        // ---------------------------------------------------------------------------------

        // manage loan do edit function-----------------------------------------------------
        public function do_edit_loan(Request $request, $id)
        {
            $this->validate($request, [
                'payable'           => 'required|min:1'
            ]);

            $total_paid = $request->payable + $request->already_paid;

            $data = manage_loan::find($id);
            $data->bayar            = $total_paid;
            $data->last_modify_date = date('Y-m-d H:i:s');
            if($total_paid == $request->total){
                $data->status_loan  = 0;
            }
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Loan';
                $table_logs  = 'lt_loan';
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

            $data_loan = new manage_loan_history;
            $data_loan->loan_id                      = $id;
            $data_loan->total_loan                   = $request->total;
            $data_loan->total_pembayaran_loan        = $request->payable;
            $data_loan->created_date                 = date('Y-m-d H:i:s');
            $data_loan->last_modify_date             = date('Y-m-d H:i:s');
            $data_loan->modify_user_id               = Auth::user()->karyawan_id;
            $data_loan->status                       = 'A';
            $data_loan->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Loan | Do Add Loan History';
                $table_logs  = 'tr_loan_history';
                $id_logs     = $data_loan->id;
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

            return redirect(url('finance/loan_home'))->with('status', ' Updated loan has been success.');
        }
        // ---------------------------------------------------------------------------------

        // loan export function-------------------------------------------------------------
        public function loan_export(Request $request)
        {
            //get request
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $idloan         = $request->idloan;
            $iduser         = $request->iduser;
            $typeuser       = $request->typeuser;

            // validate return date if null
            if($start != ""){
                    //convert date
                    $date_start = strtotime($start);
                    $date_start_format = date("Y-m-d",$date_start);
                    $date_end = strtotime($end);
                    $date_end_format = date("Y-m-d",$date_end);
            }else{
                    $date_start_format = $start;
                    $date_end_format = $end;
            }

            $date_sc            = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
            $idloan_sc          = $idloan != "" ? "AND a.loan_id LIKE '%".$idloan."%'" : "";
            if($typeuser=="employee"){
                $iduser_sc = $iduser != "" ? "AND d.karyawan_id LIKE '%".$iduser."%'" : "";
            } else if($typeuser=="customer"){
                $iduser_sc = $iduser != "" ? "AND b.customer_id LIKE '%".$iduser."%'" : "";
            } else if($typeuser=="supplier"){
                $iduser_sc = $iduser != "" ? "AND c.supplier_id LIKE '%".$iduser."%'" : "";
            } else {
                $iduser_sc = $iduser != "" ? "AND e.biayalain_id LIKE '%".$iduser."%'" : "";
            }

            $lt_loan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.*,
                            d.nama AS 'employee_name', b.nama AS 'customer_name', c.nama AS 'supplier_nama'
                            FROM lt_loan a LEFT JOIN ms_customer b
                            ON a.user_id = b.customer_id
                            LEFT JOIN ms_supplier c
                            ON a.user_id = c.supplier_id
                            LEFT JOIN ms_karyawan d
                            ON a.user_id = d.karyawan_id
                            LEFT JOIN lt_biayalain e
                            ON a.user_id = e.biayalain_id
                            WHERE a.status = 'A'
                            AND a.status_loan <> '2'
                            $date_sc $idloan_sc $iduser_sc");

            $arraydata = [$date_start_format,$date_end_format,$idloan,$typeuser,$iduser];

            Excel::create('Data Loan : '.date("d-m-Y").'', function($result) use($lt_loan,$arraydata)
            {
                $result->sheet('Data Loan', function($sheet) use($lt_loan,$arraydata)
                {
                    $i = 1;
                    $count = 0;
                    foreach($lt_loan as $item){
                        $item->user_id;
                        if($item->loan_type != 1){
                            $array2 = $item->loan_id .' - '. $item->user_id;
                        } else {
                            $array2 = $item->loan_id;
                        }

                        if(substr($item->user_id,0,1) == 'E'){
                            $arrayuser = $item->employee_name .' - '. $item->user_id;
                        } else if(substr($item->user_id,0,1) == 'C'){
                            $arrayuser = $item->customer_name .' - '. $item->user_id;
                        } else if(substr($item->user_id,0,1) == 'S'){
                            $arrayuser = $item->supplier_name .' - '. $item->user_id;
                        } else if(substr($item->user_id,0,1) == 'A'){
                            $arrayuser = $item->user_id;
                        }

                        if($item->status_loan == 1){
                            $statusloan = 'UNPAID';
                        } else {
                            $statusloan = 'PAID';
                        }

                        $i++;
                        $count++;
                        $data=[];
                        array_push($data, array(
                            $item->createddate,
                            $array2,
                            $arrayuser,
                            number_format($item->total),
                            number_format($item->bayar),
                            number_format($item->total - $item->bayar),
                            $statusloan,
                            date("d/m/Y",strtotime($item->tanggal_jatuh_tempo)),
                            ));
                        $sheet->fromArray($data, null, 'A10', false, false);
                    }

                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','LOAN EXPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($arraydata[0]=="1970-01-01"){
                        $sheet->row(7, array('Date Start : ','ALL RANGE'));
                        $sheet->row(8, array('Date End :','ALL RANGE'));
                    } else {
                        $sheet->row(7, array('Date Start : ',$arraydata[0]));
                        $sheet->row(8, array('Date End :',$arraydata[1]));
                    }

                    $sheet->row(9, array('Created Date', 'Loan ID','Loan User','Grand Total', 'Loan Alredy Paid', 'Total Loan payable', 'Status Loan', 'Due Date'));


                    $sheet->setBorder('A9:H9', 'thin');

                    // set style column
                    $sheet->cells('A9:H9', function($cells){
                        $cells->setFontSize('13');
                        $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:H1');
                    $sheet->cells('A1:H1', function($cells){
                            $cells->setFontSize('15');
                            $cells->setAlignment('center');
                    });

                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':H'.$k, 'thin');
                    }
                    for($k=1;$k<=$i+9;$k++){
                        $sheet->setHeight(array
                        (
                            $k => '20',
                        ));
                    }

                    // Header
                    $sheet->cells('B3', function($cells){
                            $cells->setFontSize('15');
                    });
                    // title
                    $sheet->cells('B4', function($cells){
                            $cells->setFontSize('15');
                    });
                    // total count
                    $sheet->cells('B6', function($cells){
                            $cells->setAlignment('left');
                    });

                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                });
            })->download('xls');

            // return to view
            return view('finance/loan/loanhome', ['lt_loan' => $lt_loan, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage loan --------------------------------------------------------------------
}
