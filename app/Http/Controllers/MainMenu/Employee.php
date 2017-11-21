<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_karyawan;
use App\Models\manage_loan;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;


class Employee extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage employee-----------------------------------------------------------------

        // manage employee index function---------------------------------------------------
        public function index_emp()
        {
            // query left join
            $ms_karyawan = DB::table('ms_karyawan')
                        ->select('ms_karyawan.*', 'ms_jabatan.name AS position_name')
                        ->leftJoin('ms_jabatan', 'ms_karyawan.jabatan', '=', 'ms_jabatan.id')
                        ->where('ms_karyawan.status', 'A')
                        ->get();

            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Employee';
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
            return view('mainmenu/employee/emphome', ['ms_karyawan' => $ms_karyawan, 'ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

        //---------------------------------------------------------------------------------
        public function detail_emp($id)
        {
          // query left join
          $sellings = DB::table('ms_penjualan')
                    ->select('ms_penjualan.*', 'tr_detail_penjualan.*','lt_piutang.status_piutang AS status_piutang',
                     'ms_customer.customer_id AS custid', 'ms_customer.nama AS namacustomer')
                    ->leftjoin('lt_piutang', 'lt_piutang.penjualan_id', '=', 'ms_penjualan.id')
                    ->leftjoin('ms_customer', 'ms_customer.customer_id', '=', 'ms_penjualan.customer_id')
                    ->join('tr_detail_penjualan', 'tr_detail_penjualan.detail_penjualan_id', '=', 'ms_penjualan.id')
                    ->where('ms_penjualan.status', 'A')
                    ->get();

          $lt_loan = DB::table('lt_loan')
                      ->select('lt_loan.*','ms_karyawan.nama AS employee_name')
                      ->leftJoin('ms_karyawan', 'lt_loan.user_id', '=', 'ms_karyawan.karyawan_id')
                      ->leftJoin('lt_biayalain', 'lt_loan.user_id', '=', 'lt_biayalain.biayalain_id')
                      ->where('lt_loan.status_loan', '<>', 2)
                      ->where('lt_loan.status', 'A')
                      ->where('lt_loan.user_id', $id)
                      ->get();


          $ms_karyawan = DB::table('ms_karyawan')
                      ->select('ms_karyawan.*', 'ms_jabatan.name AS position_name')
                      ->leftJoin('ms_jabatan', 'ms_karyawan.jabatan', '=', 'ms_jabatan.id')
                      ->where('ms_karyawan.karyawan_id', $id)
                      ->first();

          return view('mainmenu/employee/empdetail')->with('emp', $ms_karyawan)
                                                    ->with('sellings', $sellings)
                                                    ->with('lt_loan', $lt_loan);
        }
        //---------------------------------------------------------------------------------

        // manage employee add function-----------------------------------------------------
        public function add_emp()
        {
            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Employee';
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

            return view('mainmenu/employee/empadd', ['ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

        // manage employee do add function--------------------------------------------------
        public function do_add_emp(Request $request)
        {
            $this->validate($request, [
                'karyawan_name'             => 'required|max:100',
                'karyawan_address'          => 'required|max:150',
                'karyawan_telp'             => 'required|max:15',
                'position_id'               => 'required|min:1',
                'karyawan_dob'              => 'required',
                'karyawan_status'           => 'required|min:1',
                'norek'                     => 'required|min:1',
                'card_name'                 => 'required|min:2|max:100',
                'bank_name'                 => 'required|min:2|max:100'
            ]);

            // validate increment id
                $id = DB::table('ms_karyawan')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->karyawan_id,3,7);
                    $next_id    = $lastnumber + 1;
                    $id         = "EMP".sprintf("%07d", $next_id);
                }else{
                    $id         = "EMP0000001";
                }
            // validate increment id

            $data = new manage_karyawan;
            $data->karyawan_id                  = $id;
            $data->nama                         = $request->karyawan_name;
            $data->alamat                       = $request->karyawan_address;
            $data->no_telp                      = $request->karyawan_telp;
            $data->no_hp                        = $request->karyawan_hp;
            $data->alamat_asal                  = $request->karyawan_address_2;
            $data->jabatan                      = $request->position_id;
            $data->tanggal_lahir                = $request->karyawan_dob;
            $data->tempat_lahir                 = $request->karyawan_bop;
            $data->status_karyawan              = $request->karyawan_status;
            $data->no_rekening                  = $request->norek;
            $data->nama_rekening                = $request->card_name;
            $data->bank_nama                    = $request->bank_name;
            $data->nama_emergency_karyawan      = $request->emergency_name;
            $data->alamat_emergency_karyawan    = $request->emergency_address_2;
            $data->no_kontak_emergency_karyawan = $request->emergency_contact;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Employee';
                $table_logs  = 'ms_barang';
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

            $data_loan = new manage_loan;
            $data_loan->loan_id                  = $id_loan;
            $data_loan->user_id                  = $id;
            $data_loan->total                    = 0;
            $data_loan->bayar                    = 0;
            $data_loan->status_loan              = 2;
            $data_loan->loan_type                = 1;
            $data_loan->tanggal_jatuh_tempo      = date('Y-m-d H:i:s');
            $data_loan->created_date             = date('Y-m-d H:i:s');
            $data_loan->last_modify_date         = date('Y-m-d H:i:s');
            $data_loan->modify_user_id           = Auth::user()->karyawan_id;
            $data_loan->status                   = 'A';
            $data_loan->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Employee | Do Add Employee Loan';
                $table_logs  = 'lt_loan';
                $id_logs     = $data_loan->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($data_loan);

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

            return redirect(url('mainmenu/emp_home'))->with('status', ' Created new employee has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage employee edit function----------------------------------------------------
        public function edit_emp($id)
        {
            $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

            $ms_karyawan = DB::table('ms_karyawan')
                        ->select('ms_karyawan.*', 'ms_karyawan.karyawan_id AS id_employee', 'ms_gaji.gaji AS salary', 'users.email AS email','ms_jabatan.name AS position_name')
                        ->leftJoin('ms_gaji', 'ms_karyawan.karyawan_id', '=', 'ms_gaji.karyawan_id')
                        ->leftJoin('users', 'ms_karyawan.karyawan_id', '=', 'users.karyawan_id')
                        ->leftJoin('ms_jabatan', 'ms_karyawan.jabatan', '=', 'ms_jabatan.id')
                        ->where('ms_karyawan.status', 'A')
                        ->where('ms_karyawan.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Employee';
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

            return view('mainmenu/employee/empedit', ['ms_karyawan' => $ms_karyawan, 'ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

        // manage employee do edit function-------------------------------------------------
        public function do_edit_emp(Request $request, $id)
        {
            $this->validate($request, [
                'karyawan_id'               => 'required|min:1',
                'karyawan_name'             => 'required|max:100',
                'karyawan_address'          => 'required|max:150',
                'karyawan_telp'             => 'required|max:15',
                'position_id'               => 'required|min:1',
                'karyawan_dob'              => 'required',
                'karyawan_status'           => 'required|min:1',
                'norek'                     => 'required|min:1',
                'card_name'                 => 'required|min:2|max:100',
                'bank_name'                 => 'required|min:2|max:100'
            ]);

            $data = manage_karyawan::find($id);
            $data->nama                         = $request->karyawan_name;
            $data->alamat                       = $request->karyawan_address;
            $data->no_telp                      = $request->karyawan_telp;
            $data->no_hp                        = $request->karyawan_hp;
            $data->alamat_asal                  = $request->karyawan_address_2;
            $data->jabatan                      = $request->position_id;
            $data->tanggal_lahir                = $request->karyawan_dob;
            $data->tempat_lahir                 = $request->karyawan_bop;
            $data->status_karyawan              = $request->karyawan_status;
            $data->no_rekening                  = $request->norek;
            $data->nama_rekening                = $request->card_name;
            $data->bank_nama                    = $request->bank_name;
            $data->nama_emergency_karyawan      = $request->emergency_name;
            $data->alamat_emergency_karyawan    = $request->emergency_address_2;
            $data->no_kontak_emergency_karyawan = $request->emergency_contact;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Employee';
                $table_logs  = 'ms_barang';
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

            return redirect(url('mainmenu/emp_home'))->with('status', ' Updated user has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage employee delete function--------------------------------------------------
        public function delete_emp($id)
        {
            $data = manage_karyawan::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Employee';
                $table_logs  = 'ms_barang';
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

            return redirect(url('mainmenu/emp_home'))->with('status', ' Deleted employee has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage employee range function---------------------------------------------------
        public function emp_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idemp;
            $name       = $request->name;
            $phone      = $request->phone;
            $position   = $request->position;
            $statusemp  = $request->status;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $phone == "" &&  $position == "" && $statusemp == ""){
                return redirect(url('mainmenu/emp_home'));

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
                $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
                $position_sc    = $position != "" ? "AND a.jabatan = '".$position."'" : "";
                $statusemp_sc   = $statusemp != "" ? "AND a.status_karyawan = '".$statusemp."'" : "";

                // query range
                $ms_karyawan = DB::select("SELECT a.*, b.name AS 'position_name'
                                FROM ms_karyawan a LEFT JOIN ms_jabatan b
                                ON a.jabatan = b.id
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $phone_sc $position_sc $statusemp_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$id,$name,$phone,$position,$statusemp];

                // load dropdown position
                $ms_jabatan = DB::table('ms_jabatan')
                        ->where('status', 'A')
                        ->get();

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Employee';
                    $table_logs  = 'ms_barang';
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

                return view('mainmenu/employee/emphome')->with('ms_karyawan',$ms_karyawan)->with('arraydate',$arraydate)->with('ms_jabatan',$ms_jabatan);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage employee export function--------------------------------------------------
        public function emp_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idemp;
            $name       = $request->name;
            $phone      = $request->phone;
            $position   = $request->position;
            $statusemp  = $request->status;

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
            $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
            $position_sc    = $position != "" ? "AND a.jabatan = '".$position."'" : "";
            $statusemp_sc   = $statusemp != "" ? "AND a.status_karyawan = '".$statusemp."'" : "";

            // validate export date
                if($request->dateStart != ""){

                    $ms_karyawan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'registerdate',
                            a.karyawan_id AS 'employeeid',
                            a.nama AS 'name', b.`name` AS 'position', a.alamat AS 'address', a.no_telp AS 'telephone',
                            a.no_hp AS 'handphone', a.alamat_asal AS 'address2', a.tempat_lahir AS 'pob',
                            DATE_FORMAT(a.tanggal_lahir,'%d/%m/%Y') AS 'dob', a.status_karyawan AS 'employeestatus',
                            a.no_rekening AS 'accountnumber', a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname',
                            a.nama_emergency_karyawan AS 'emername', a.alamat_emergency_karyawan AS 'emeraddress',
                            a.no_kontak_emergency_karyawan AS 'emercontact'
                            FROM ms_karyawan a LEFT JOIN ms_jabatan b
                            ON a.jabatan = b.id
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $name_sc $phone_sc $position_sc $statusemp_sc
                            ORDER BY a.created_date DESC");

                }else{

                    $ms_karyawan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'registerdate',
                            a.karyawan_id AS 'employeeid',
                            a.nama AS 'name', b.`name` AS 'position', a.alamat AS 'address', a.no_telp AS 'telephone',
                            a.no_hp AS 'handphone', a.alamat_asal AS 'address2', a.tempat_lahir AS 'pob',
                            DATE_FORMAT(a.tanggal_lahir,'%d/%m/%Y') AS 'dob', a.status_karyawan AS 'employeestatus',
                            a.no_rekening AS 'accountnumber', a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname',
                            a.nama_emergency_karyawan AS 'emername', a.alamat_emergency_karyawan AS 'emeraddress',
                            a.no_kontak_emergency_karyawan AS 'emercontact'
                            FROM ms_karyawan a LEFT JOIN ms_jabatan b
                            ON a.jabatan = b.id
                            WHERE a.`status` = 'A'
                            $id_sc $name_sc $phone_sc $position_sc $statusemp_sc
                            ORDER BY a.created_date DESC");
                }
            // validate export date

            Excel::create('Data Employee - '.date("d-m-Y").'', function($result) use ($ms_karyawan, $date_start_format, $date_end_format) {

                $result->sheet('Data Employee', function($sheet) use($ms_karyawan,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_karyawan as $item){
                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->registerdate,
                                $item->employeeid,
                                $item->name,
                                $item->position,
                                $item->address,
                                $item->telephone,
                                $item->handphone,
                                $item->address2,
                                $item->pob,
                                $item->dob,
                                $item->employeestatus,
                                $item->accountnumber,
                                $item->cardname,
                                $item->bankname,
                                $item->emername,
                                $item->emeraddress,
                                $item->emercontact
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','EMPLOYEE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));

                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Registration Date','Employee ID','Name','Position','Address','Telephone','Handphone','Other Address','Place of Birth','Date of Birth','Employee Status','Account Number','Card Name','Bank Name','Emergency Name','Emergency Address','Emergency Contact'));
                    $sheet->setBorder('A9:Q9', 'thin');

                    // set style column
                    $sheet->cells('A9:Q9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:Q1');
                    $sheet->cells('A1:Q1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('left');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':Q'.$k, 'thin');
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

            // $arraydate = [$start,$end];
            $arraydate = [$start,$end,$id,$name,$phone,$position,$statusemp];

            // load dropdown position
            $ms_jabatan = DB::table('ms_jabatan')
                    ->where('status', 'A')
                    ->get();

            // return to view
            return view('mainmenu/employee/emphome', ['ms_karyawan' => $ms_karyawan, 'arraydate' => $arraydate, 'ms_jabatan' => $ms_jabatan]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage employee-----------------------------------------------------------------
}
