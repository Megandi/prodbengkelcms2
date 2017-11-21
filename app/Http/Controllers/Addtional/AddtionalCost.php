<?php

namespace App\Http\Controllers\Addtional;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_biayalain;
use App\Models\manage_biayalain_detail;
use App\Models\manage_loan;
use App\Models\manage_loan_history;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class AddtionalCost extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu addtional cost------------------------------------------------------------------

        // addtional cost index function----------------------------------------------------
        public function index_addtional()
        {
            $lt_biayalain = DB::select("SELECT a.*, SUM(b.jumlah) AS 'totaljumlah_detail'
            				FROM lt_biayalain a LEFT JOIN lt_biayalain_detail b
            				ON a.id = b.biayalain_id
            				WHERE a.status = 'A'
            				GROUP BY a.id");

            return view('addtional/addtional/addhome', ['lt_biayalain' => $lt_biayalain]);
        }
        // ---------------------------------------------------------------------------------

        // addtional cost add function------------------------------------------------------
        public function add_addtional()
        {
            $validasi = 0;
            return view('addtional/addtional/costadd')->with('validasi', $validasi);
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

            return view('addtional/addtional/costadd_next')->with('biayalain', $biayalain)->with('validasi', $validasi)->with('total', $total)->with('totalharga', $totalharga);
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
                    return redirect(url('addtional/addtional_home/add'))->with('error', ' Total = 0, Please check the total value.');
                } else {
                    return redirect(url('addtional/addtional_home/addnext/'.$msid))->with('error', ' Total = 0, Please check the total value.');
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

            return redirect(url('addtional/addtional_home/addnext/'.$msid))->with('status', ' Created new buying has been success.');
        }
        // ---------------------------------------------------------------------------------

        // addtional cost edit function-----------------------------------------------------
        public function edit_addtional($id)
        {
            $lt_biayalain = manage_biayalain::where('status', 'A')
                        ->where('id', $id)
                        ->first();

            return view('addtional/addtional/costedit', ['lt_biayalain' => $lt_biayalain]);
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

            return redirect(url('addtional/addtional_home'))->with('status', ' Updated Addtional Cost has been success.');
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

            return redirect(url('addtional/addtional_home'))->with('status', ' Deleted Addtional Cost has been success.');
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

            return redirect(url('addtional/addtional_home/addnext/'.$biayalainid))->with('status', ' Deleted buying has been success.');
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

            return view('addtional/addtional/costadd_checkout')->with('total', $total)
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

            return redirect(url('addtional/addtional_home'));
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
								return redirect(url('addtional/addtional_home'));
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
            return view('addtional/addtional/addhome')->with('lt_biayalain',$lt_biayalain)->with('arraydata',$arraydata);
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
            return view('addtional/addtional/addhome')->with('lt_biayalain',$lt_biayalain)->with('arraydata',$arraydata);
        }
        // ---------------------------------------------------------------------------------

    // menu addtional cost------------------------------------------------------------------
}
