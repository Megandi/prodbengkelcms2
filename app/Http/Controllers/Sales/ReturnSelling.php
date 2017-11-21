<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_returnpenjualan;
use App\Models\manage_sales_return;
use App\Models\manage_selling;
use App\Models\manage_selling_history;
use App\Models\manage_service;
use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\deposit_cust;
use App\Models\manage_credit;
use App\Models\manage_credit_history;

use App\Models\manage_logs;
use Illuminate\Support\Facades\Input;
use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ReturnSelling extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('sales/sales/salreturnhome', ['tr_returpenjualan' => $tr_returpenjualan]);
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

           return view('sales/sales/salreturnadd')->with('idreturn', $idreturn);
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

    		return redirect(url('sales/salreturn_home/addnext/'.$retur->id));
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
                return redirect(url('sales/salreturn_home/addnext/'.$request->returid))->with('error', ' Quantities return must be less than last quantities or same.');
            }

    				$retur = manage_returnpenjualan::find($request->returid);
    				$retur->total_return = $retur->total_return + $request->items_grand_total_view;
    				$retur->save();

    				$penjualan = manage_selling::find($retur->penjualan_id);
    				$deposittt = deposit_cust::where('customer_id', $penjualan->customer_id)->first();
    				if($request->type_return==1){
    						if($deposittt->deposit==0){
    							return redirect(url('sales/salreturn_home/addnext/'.$request->returid))->with('error', ' Can not use CUT THE DEBT as Type Return because Deposit is 0.');
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
    					return redirect(url('sales/salreturn_home/addnext/cdb/'.$data->id.'/'.$request->returid.'/'.$request->items_grand_total_view));
    				} else if($request->type_return==4){
							return redirect(url('selling/selling_home/tig/add/'.$request->items_grand_total_view.'/'.$request->returid.'/'.$data->id));
						}

            return redirect(url('sales/salreturn_home/addnext/'.$request->returid))->with('status', ' Created new purchase return has been success.');
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

    		return view('sales/sales/salreturnadd_next_cdb')->with('lt_piutang', $lt_piutang)->with('customer_id', $customer_id)->with('retur_id', $id)->with('total', $total)->with('detailid', $detailid);
    	}
    	// ---------------------------------------------------------------------------------

    	// selling return do next cut the debt function-------------------------------------
    	public function saldo_addnext_cdb(Request $request)
    	{
    		$total_return = $request->total_return;
    		if(null == Input::get('cbpilih')){
    			return redirect(url('sales/salreturn_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
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

    			return redirect(url('sales/salreturn_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
    		}
    	}
    	// ---------------------------------------------------------------------------------

    	// selling return next function-----------------------------------------------------
        public function salnext_return($id)
        {
    		$return = manage_returnpenjualan::find($id);
    		$penjualan = manage_selling::find($return->penjualan_id);

    		return view('sales/sales/salreturnadd_next')->with('return',$return)->with('penjualan',$penjualan);
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

            return view('sales/sales/salreturnedit', ['tr_returpenjualan' => $tr_returpenjualan]);
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

            return redirect(url('sales/salreturn_home'))->with('status', ' Updated sales return has been success.');
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

            return redirect(url('sales/salreturn_home'))->with('status', ' Deleted sales return has been success.');
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
					return redirect(url('sales/salreturn_home'));
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
                return view('sales/sales/salreturnhome')->with('tr_returpenjualan',$tr_returpenjualan)->with('arraydata',$arraydata);
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
                    $sheet->row(9, array('Created Date', 'ID Sales Return', 'Selling ID','Total QTY','Total','Status'));


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
            return view('sales/sales/salreturnhome', ['tr_returpenjualan' => $tr_returpenjualan, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu selling return------------------------------------------------------------------
}
