<?php

namespace App\Http\Controllers\Selling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_credit;
use App\Models\manage_credit_history;
use App\Models\manage_selling;
use App\Models\manage_selling_history;
use App\Models\deposit_cust;
use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Credit extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('selling/credit/credithome')->with('lt_piutang', $lt_piutang)->with('arraydata', $arraydata);
        }
        // ---------------------------------------------------------------------------------

        // manage selling edit function-----------------------------------------------------
        public function edit_credit($id)
        {
    		$lt_piutang = manage_credit::find($id);
            return view('selling/credit/creditedit', ['lt_piutang' => $lt_piutang]);
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
    			return redirect(url('selling/credit_home/edit/'.$credit_id))->with('error', ' Pay can not more than must paid .');
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
            return redirect(url('selling/credit_home'))->with('status', ' Updated credit has been success.');
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
    				return redirect(url('selling/credit_home'));
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
                return view('selling/credit/credithome')->with('lt_piutang', $lt_piutang)->with('arraydata', $arraydata);
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
            return view('selling/credit/credithome', ['lt_piutang' => $lt_piutang, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage credit-------------------------------------------------------------------
}
