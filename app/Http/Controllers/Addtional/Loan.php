<?php

namespace App\Http\Controllers\Addtional;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_loan;
use App\Models\manage_loan_history;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Loan extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('addtional/loan/loanhome', ['lt_loan' => $lt_loan]);
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
                    return redirect(url('addtional/loan_home'));
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
                return view('addtional/loan/loanhome')->with('lt_loan', $lt_loan)->with('arraydata', $arraydata);
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

            return view('addtional/loan/loanadd');
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

            return redirect(url('addtional/loan_home'))->with('status', ' Add Total Loan has been success.');
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

            return view('addtional/loan/loanedit', ['lt_loan' => $lt_loan]);
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

            return redirect(url('addtional/loan_home'))->with('status', ' Updated loan has been success.');
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
            return view('addtional/loan/loanhome', ['lt_loan' => $lt_loan, 'arraydata' => $arraydata]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage loan --------------------------------------------------------------------
}
