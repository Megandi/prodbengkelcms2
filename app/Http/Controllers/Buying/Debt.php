<?php

namespace App\Http\Controllers\Buying;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_debt;
use App\Models\manage_debt_history;
use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\deposit_supp;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Debt extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('buying/debt/debthome')->with('lt_hutang', $lt_hutang)->with('arraydata', $arraydata);
        }
        // ---------------------------------------------------------------------------------

        // manage debt edit function--------------------------------------------------------
        public function edit_debt($id)
        {
    		$lt_hutang = manage_debt::find($id);
            return view('buying/debt/debtedit', ['lt_hutang' => $lt_hutang]);
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
				return redirect(url('buying/debt_home/edit/'.$debt_id))->with('error', ' Pay can not more than must paid .');
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

            return redirect(url('buying/debt_home'))->with('status', ' Updated credit has been success.');
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
					return redirect(url('buying/debt_home'));
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
				return view('buying/debt/debthome')->with('lt_hutang', $lt_hutang)->with('arraydata', $arraydata);
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
            return view('buying/debt/debthome', ['lt_hutang' => $lt_hutang, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage debt---------------------------------------------------------------------
}
