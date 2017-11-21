<?php

namespace App\Http\Controllers\Selling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_selling;
use App\Models\manage_selling_history;
use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\manage_service;
use App\Models\manage_credit;
use App\Models\manage_credit_history;
use App\Models\deposit_cust;
use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ManageSelling extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('selling/selling/sellinghome', ['sellings' => $sellings]);
        }
        // ---------------------------------------------------------------------------------

        //manage buying detial----------------------------------------------
        public function detail_selling($id)
        {
          $selling = manage_selling::find($id);
          return view('selling/selling/sellingdetail')->with('selling', $selling);
        }
        //------------------------------------------------------------------


        // manage selling select2 items function--------------------------------------------
        public function search_items_selling()
        {
            $row_set = [];
            $type = strip_tags(trim($_GET['type']));
            if($type==2){
            $term = strip_tags(trim($_GET['q']));

            $ms_barang = DB::table('ms_barang')
                            ->where('ms_barang.status',"A")
                            ->where('ms_barang.is_available',1)
														->where('ms_barang.stock','>','0')
                            ->where('ms_barang.barang_id','like', "%".$term."%" )
                            ->orWhere('ms_barang.nama','like', "%".$term."%" )
                            ->orWhere('ms_barang.created_date','like', "%".$term."%" )
                            ->get();

            $query = $ms_barang;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->id));
                    $new_row['text']=htmlentities(stripslashes($row->barang_id ." - ". $row->nama ." - ". date('d/m/Y', strtotime($row->created_date))));
                    $new_row['qty']=htmlentities(stripslashes($row->stock));
                    $new_row['sub_total']=htmlentities(stripslashes($row->harga));
                    $new_row['sell_price']=htmlentities(stripslashes($row->harga_jual));
                    $new_row['id_get_items']=htmlentities(stripslashes($row->id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
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
            return view('selling/selling/sellingadd')->with('validasi', $validasi);
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
						return redirect(url('selling/selling_home/add'))->with('error', ' Quantities not Enough.');
					} else {
						return redirect(url('selling/selling_home/addnext/'.$request->id_penjualan))->with('error', ' Quantities not Enough.');
					}
				}
    		}

    		// calculate total pembelian-------------------------------------------------------------
    		$total_price = $request->items_qty_view * $request->items_sub_total_view;
    		// calculate total pembelian-------------------------------------------------------------

    		// validasi quantities-------------------------------------------------------------------
    		if($total_price == 0){
    			if($request->validasi==0){
    				return redirect(url('selling/selling_home/add'))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
    			} else {
    				return redirect(url('selling/selling_home/addnext/'.$msid))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
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

    		return redirect(url('selling/selling_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
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
                    return redirect(url('selling/selling_home/add'))->with('error', ' Quantities not Enough.');
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
            return redirect(url('selling/selling_home/add_next',[$request->penjualan_id]));
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

            return view('selling/selling/sellingaddnext')->with('selling', $selling)->with('validasi', $validasi)->with('total', $total);
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
					return redirect(url('selling/selling_home/addnext/'.$sellingid))->with('status', ' Deleted buying has been success.');
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

    		return view('selling/selling/sellingcheckout')->with('total', $total)
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
    		        return redirect(url('selling/selling_home/checkout'))->with('error', ' Down payment must less than Grand Total');
    		    }

    		    if($request->sell_dp == $total_price){
    		        return redirect(url('selling/selling_home/checkout'))->with('error', ' Down payment must less than Grand Total');
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

    		return redirect(url('selling/selling_home'));
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

            return view('selling/selling/sellingedit', ['selling' => $selling, 'total' => $total]);
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

            return redirect(url('selling/selling_home'))->with('status', ' Updated buying has been success.');
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

			return redirect(url('selling/selling_home'))->with('status', ' Deleted buying has been success.');
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
					return redirect(url('selling/selling_home'));

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
				return view('selling/selling/sellinghome')->with('sellings', $sellings)->with('arraydata', $arraydata);
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
            return view('selling/selling/sellinghome', ['sellings' => $sellings, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage selling------------------------------------------------------------------
}
