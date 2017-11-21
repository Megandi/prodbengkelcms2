<?php

namespace App\Http\Controllers\Buying;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\manage_debt;
use App\Models\manage_debt_history;
use App\Models\deposit_supp;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ManageBuying extends Controller
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

            return view('buying/buying/buyinghome', ['buyings' => $buyings]);
        }
        // ---------------------------------------------------------------------------------

        //manage buying detial----------------------------------------------
        public function detail_buying($id)
        {
          $buying = manage_buying::find($id);
          return view('buying/buying/buyingdetail')->with('buying', $buying);
        }
        //------------------------------------------------------------------

        // manage buying add function-------------------------------------------------------
        public function add_buying()
        {
    		$validasi = 0;
            return view('buying/buying/buyingadd')->with('validasi', $validasi);
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

            return view('buying/buying/buying_addnext')->with('buying', $buying)
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
                return redirect(url('buying/buying_home/add'))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    					} else {
    						return redirect(url('buying/buying_home/addnext/'.$msid))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
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

            return redirect(url('buying/buying_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
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

            return view('buying/buying/buyingedit', ['buying' => $buying, 'total' => $total]);
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

            return redirect(url('buying/buying_home'))->with('status', ' Updated buying has been success.');
        }
        // ---------------------------------------------------------------------------------

    	// manage buying delete buying detail function--------------------------------------
    	public function delete_buying_detail($detailid,$buyingid)
    	{
    		$detail = manage_buying_history::find($detailid);
    		manage_items_temp::destroy($detail->barang_id);
    		manage_buying_history::destroy($detailid);
    		return redirect(url('buying/buying_home/addnext/'.$buyingid))->with('status', ' Deleted buying has been success.');
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

			return view('buying/buying/checkout')->with('total', $total)->with('buying', $buying);
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
			        return redirect(url('buying/buying_home/checkout'))->with('error', ' Down payment must less than Grand Total');
			    }

			    if($request->sell_dp == $total_price){
			        return redirect(url('buying/buying_home/checkout'))->with('error', ' Down payment must less than Grand Total');
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

			return redirect(url('buying/buying_home'));
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
            return redirect(url('buying/buying_home'))->with('status', ' Deleted buying has been success.');
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
    				return redirect(url('buying/buying_home'));
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
                    return view('buying/buying/buyinghome')->with('buyings', $buyings)->with('arraydata', $arraydata);
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
            return view('buying/buying/buyinghome', ['buyings' => $buyings, 'arraydata' => $arraydata]);
        }
        // manage debt export function------------------------------------------------------

    // menu manage buying-------------------------------------------------------------------

}
