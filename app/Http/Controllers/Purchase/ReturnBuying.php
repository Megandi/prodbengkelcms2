<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_returnpembelian;
use App\Models\manage_purchase_return;
use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\deposit_supp;
use App\Models\manage_debt;
use App\Models\manage_debt_history;

use App\Models\manage_logs;
use Illuminate\Support\Facades\Input;
use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ReturnBuying extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('purchase/purchase/returnhome', ['tr_returpembelian' => $tr_returpembelian]);
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

            return view('purchase/purchase/returnadd')->with('idreturn', $idreturn);
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

    		return redirect(url('purchase/return_home/addnext/'.$retur->id));
    	}
    	// ---------------------------------------------------------------------------------

    	// purchase return next return function---------------------------------------------
        public function next_return($id)
        {
    		$return = manage_returnpembelian::find($id);
    		$pembelian = manage_buying::find($return->pembelian_id);

    		return view('purchase/purchase/returnadd_next')->with('return',$return)->with('pembelian',$pembelian);
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
                return redirect(url('purchase/return_home/addnext/'.$request->returid))->with('error', ' Quantities return must be less than last quantities has return.');
            }

    				$retur = manage_returnpembelian::find($request->returid);
    				$retur->total_return = $retur->total_return + $request->items_grand_total_view;
    				$retur->save();

    				$pembelian = manage_buying::find($retur->pembelian_id);
    				$deposittt = deposit_supp::where('supplier_id', $pembelian->supplier_id)->first();
    				if($request->type_return==1){
    						if($deposittt->deposit==0){
    							return redirect(url('purchase/return_home/addnext/'.$request->returid))->with('error', ' Can not use CUT THE DEBT as Type Return because Deposit is 0.');
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
    					return redirect(url('purchase/return_home/addnext/cdb/'.$data->id.'/'.$request->returid.'/'.$request->items_grand_total_view));
    				} else if($request->type_return==4){
							return redirect(url('buying/buying_home/tig/add/'.$request->items_grand_total_view.'/'.$request->returid.'/'.$data->id));
						}

            return redirect(url('purchase/return_home/addnext/'.$request->returid))->with('status', ' Created new purchase return has been success.');
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

    		return view('purchase/purchase/returnadd_next_cdb')->with('lt_hutang', $lt_hutang)
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
    			return redirect(url('purchase/return_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
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
              $debthistory->returndetail_id       	= $request->detailid;
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

    			return redirect(url('purchase/return_home/addnext/'.$request->retur_id))->with('status', ' Total money to be paid with cash : Rp '.number_format($total_return,'2'));
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

            return view('purchase/purchase/returnedit', ['tr_returpembelian' => $tr_returpembelian]);
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

            return redirect(url('purchase/return_home'))->with('status', ' Updated purchase return has been success.');
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
            return redirect(url('purchase/return_home'))->with('status', ' Deleted purchase return has been success.');
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
					return redirect(url('purchase/return_home'));
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
    			return view('purchase/purchase/returnhome')->with('tr_returpembelian',$tr_returpembelian)->with('arraydata',$arraydata);
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
            return view('purchase/purchase/returnhome', ['tr_returpembelian' => $tr_returpembelian, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu purchase return-----------------------------------------------------------------
}
