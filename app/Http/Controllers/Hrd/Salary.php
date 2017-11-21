<?php

namespace App\Http\Controllers\Hrd;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_salary;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Salary extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage salary-------------------------------------------------------------------

        // manage salary index function-----------------------------------------------------
        public function index_sal()
        {
            // query left join
            $ms_gaji = DB::table('ms_gaji')
                        ->select('ms_gaji.*', 'ms_karyawan.karyawan_id AS id_employee', 'ms_karyawan.nama AS name_employee',
                            'ms_karyawan.no_rekening AS accountnumber', 'ms_karyawan.nama_rekening AS cardname', 'ms_karyawan.bank_nama AS bankname')
                        ->leftJoin('ms_karyawan', 'ms_gaji.karyawan_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('ms_gaji.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Salary';
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
            return view('hrd/salary/salhome', ['ms_gaji' => $ms_gaji]);
        }
        // ---------------------------------------------------------------------------------

        // manage salary select2 employee function------------------------------------------
        public function search_employee()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_karyawan = DB::table('ms_karyawan')
                            ->where('status',"A")
                            ->where('nama','like', "%".$term."%")
                            ->orWhere('karyawan_id','like', "%".$term."%")
                            ->get();

            $query = $ms_karyawan;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->karyawan_id));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $new_row['text']=htmlentities(stripslashes($row->karyawan_id." - ".$row->nama));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage salary add function-------------------------------------------------------
        public function add_sal()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Salary';
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

            return view('hrd/salary/saladd');
        }
        // ---------------------------------------------------------------------------------

        // manage salary do add function----------------------------------------------------
        public function do_add_sal(Request $request)
        {
            $this->validate($request, [
                'karyawan_id'     => 'required|min:1',
                'karyawan_name'   => 'required|max:100',
                'salary'          => 'required|max:20'
            ]);

            $data = new manage_salary;
            $data->karyawan_id                  = $request->karyawan_id;
            $data->nama                         = $request->karyawan_name;
            $data->gaji                         = $request->salary;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = "A";
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Salary'; 
                $table_logs  = 'ms_gaji';
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

            return redirect(url('hrd/sal_home'))->with('status', ' Add new salary has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage salary edit function------------------------------------------------------
        public function edit_sal($id)
        {

            $ms_gaji = DB::table('ms_gaji')
                        ->select('ms_gaji.*', 'ms_karyawan.karyawan_id AS id_employee', 'ms_karyawan.nama AS name_employee')
                        ->leftJoin('ms_karyawan', 'ms_gaji.karyawan_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('ms_gaji.status', 'A')
                        ->where('ms_gaji.id', $id)
                        ->first();

            // save logs---------------------------------------------------
            $do_logs     = 'Open Edit Manage Salary';
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

            return view('hrd/salary/saledit', ['ms_gaji' => $ms_gaji]);
        }
        // ---------------------------------------------------------------------------------

        // manage salary do edit function---------------------------------------------------
        public function do_edit_sal(Request $request, $id)
        {
            $this->validate($request, [
                'karyawan_id'     => 'required|min:1',
                'karyawan_name'   => 'required|max:100',
                'salary'          => 'required|max:20'
            ]);

            $data = manage_salary::find($id);
            $data->karyawan_id                  = $request->karyawan_id;
            $data->nama                         = $request->karyawan_name;
            $data->gaji                         = $request->salary;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Salary'; 
                $table_logs  = 'ms_gaji';
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

            return redirect(url('hrd/sal_home'))->with('status', ' Updated salary has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage salary delete function----------------------------------------------------
        public function delete_sal($id)
        {
            $data = manage_salary::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
            $do_logs     = 'Do Delete Salary'; 
            $table_logs  = 'ms_gaji';
            $id_logs     = $data->id;
            $url_logs    = url()->current();
            $ip_logs     = $_SERVER['REMOTE_ADDR'];

            $data = new manage_logs;
            $data->do                  = $do_logs;
            $data->table               = $table_logs;
            $data->primary             = $id_logs;
            $data->url                 = $url_logs;
            $data->ip                  = $ip_logs;

            $data->created_date        = date('Y-m-d H:i:s');
            $data->last_modify_date    = date('Y-m-d H:i:s');
            $data->modify_user_id      = Auth::user()->karyawan_id;
            $data->status              = 'A';
            $data->save();
            // save logs---------------------------------------------------

            return redirect(url('hrd/sal_home'))->with('status', ' Deleted salary has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage salary range function-----------------------------------------------------
        public function sal_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idemp;
            $name       = $request->name;
            $salary     = $request->salary;
            $accnumber  = $request->accnumber;
            $accbank    = $request->accbank;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $salary == "" &&  $accnumber == "" && $accbank == ""){    
                return redirect(url('hrd/sal_home'));

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

                // set query to variable
                $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
                $id_sc          = $id != "" ? "AND a.karyawan_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $salary_sc      = $salary != "" ? "AND a.gaji LIKE '%".$salary."%'" : "";
                $accnumber_sc   = $accnumber != "" ? "AND b.no_rekening LIKE '%".$accnumber."%'" : "";
                $accbank_sc     = $accbank != "" ? "AND b.bank_nama = '".$accbank."'" : "";

                // query range
                $ms_gaji = DB::select("SELECT a.*, b.karyawan_id AS 'id_employee', b.nama AS 'name_employee',
                                b.no_rekening AS 'accountnumber', b.nama_rekening AS 'cardname', b.bank_nama AS 'bankname'
                                FROM ms_gaji a LEFT JOIN ms_karyawan b
                                ON a.karyawan_id = b.karyawan_id
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $salary_sc $accnumber_sc $accbank_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$id,$name,$salary,$accnumber,$accbank];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Salary'; 
                    $table_logs  = 'ms_gaji';
                    $url_logs    = url()->current();
                    $ip_logs     = $_SERVER['REMOTE_ADDR'];
                    $param_logs  = json_encode($request->all());

                    $data = new manage_logs;
                    $data->do                  = $do_logs;
                    $data->table               = $table_logs;
                    $data->url                 = $url_logs;
                    $data->ip                  = $ip_logs;
                    $data->param               = $param_logs;

                    $data->created_date        = date('Y-m-d H:i:s');
                    $data->last_modify_date    = date('Y-m-d H:i:s');
                    $data->modify_user_id      = Auth::user()->karyawan_id;
                    $data->status              = 'A';
                    $data->save();
                // save logs---------------------------------------------------

                // return to view
                return view('hrd/salary/salhome', ['ms_gaji' => $ms_gaji, 'arraydate' => $arraydate]);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage salary export function----------------------------------------------------
        public function sal_export(Request $request)
        {

            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idemp;
            $name       = $request->name;
            $salary     = $request->salary;
            $accnumber  = $request->accnumber;
            $accbank    = $request->accbank;

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

            // set query to variable
                $date_sc    = $date_start_format != "" ? "AND a.created_date >= '".$date_start_format." 00:00:00' AND a.created_date <= '".$date_end_format." 23:59:59'" : "";
                $id_sc          = $id != "" ? "AND a.karyawan_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $salary_sc      = $salary != "" ? "AND a.gaji LIKE '%".$salary."%'" : "";
                $accnumber_sc   = $accnumber != "" ? "AND b.no_rekening LIKE '%".$accnumber."%'" : "";
                $accbank_sc     = $accbank != "" ? "AND b.bank_nama = '".$accbank."'" : "";

            // validate export date
                if($request->dateStart != ""){

                    $ms_gaji = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                        a.karyawan_id AS 'empid',
                        a.nama AS 'name', a.gaji AS 'amountsalary', b.no_rekening AS 'accountnumber',
                        b.nama_rekening AS 'cardname', b.bank_nama AS 'bankname'
                        FROM ms_gaji a LEFT JOIN ms_karyawan b
                        ON a.karyawan_id = b.karyawan_id
                        WHERE a.`status` = 'A'
                        $date_sc $id_sc $name_sc $salary_sc $accnumber_sc $accbank_sc
                        ORDER BY a.created_date DESC");

                }else{

                    $ms_gaji = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                        a.karyawan_id AS 'empid',
                        a.nama AS 'name', a.gaji AS 'amountsalary', b.no_rekening AS 'accountnumber',
                        b.nama_rekening AS 'cardname', b.bank_nama AS 'bankname'
                        FROM ms_gaji a LEFT JOIN ms_karyawan b
                        ON a.karyawan_id = b.karyawan_id
                        WHERE a.`status` = 'A'
                        $id_sc $name_sc $salary_sc $accnumber_sc $accbank_sc
                        ORDER BY a.created_date DESC");
                }
            // validate export date

            Excel::create('Data Salary - '.date("d-m-Y").'', function($result) use ($ms_gaji, $date_start_format, $date_end_format) {

                $result->sheet('Data Salary', function($sheet) use($ms_gaji,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_gaji as $item){
                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->empid,
                                $item->name,
                                $item->amountsalary,
                                $item->accountnumber,
                                $item->cardname,
                                $item->bankname
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SALARY REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Employee ID','Name','Amount Salary','Account Number','Card Name','Bank Name'));
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

                    // loops value
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

                    // logo-------------------------------------------------------------
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo.png')); //your image path
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(40);
                    $objDrawing->setOffsetY(5);

                    //set width, height
                    $objDrawing->setWidth(70);
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                    // logo-------------------------------------------------------------

                });

            })->download('xls');

            $arraydate = [$date_start_format,$date_end_format,$id,$name,$salary,$accnumber,$accbank];

            // return to view
            return view('hrd/salary/salhome', ['ms_gaji' => $ms_gaji, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage salary-------------------------------------------------------------------
}
