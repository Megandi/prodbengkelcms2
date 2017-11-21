<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_customer;
use App\Models\deposit_cust;
use App\Models\manage_loan;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Customer extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage customer-----------------------------------------------------------------

        // manage customer index function---------------------------------------------------
        public function index_cust()
        {
            $ms_customer = DB::table('ms_customer')
                        ->where('ms_customer.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Customer';
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
            return view('mainmenu/customer/custhome', ['ms_customer' => $ms_customer]);
        }
        // ---------------------------------------------------------------------------------


        public function detail_cust($id)
        {

          // query left join
          $sellings = DB::select("SELECT a.*, b.status_piutang AS 'status_piutang',
                  c.customer_id AS 'custid', c.nama AS 'namacustomer'
                  FROM ms_penjualan a LEFT JOIN lt_piutang b
                  ON a.id = b.penjualan_id
                  LEFT JOIN ms_customer c
                  ON a.customer_id = c.customer_id
                  LEFT JOIN tr_detail_penjualan d
                  ON a.penjualan_id = d.detail_penjualan_id
                  WHERE a.status = 'A' AND c.customer_id = '$id'");

          $lt_piutang = DB::select("SELECT a.*, b.penjualan_id AS 'b_penjualan_id', b.tanggal_jatuh_tempo_penjualan AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
      										FROM lt_piutang a LEFT JOIN ms_penjualan b
      										ON a.penjualan_id = b.id
      										WHERE a.status = 'A'
      										AND a.status_piutang <> '2'
                          AND b.customer_id = '$id'");

          $lt_loan = DB::table('lt_loan')
                      ->select('lt_loan.*','ms_customer.nama AS customer_name')
                      ->leftJoin('ms_customer', 'lt_loan.user_id', '=', 'ms_customer.customer_id')
                      ->leftJoin('lt_biayalain', 'lt_loan.user_id', '=', 'lt_biayalain.biayalain_id')
                      ->where('lt_loan.status_loan', '<>', 2)
                      ->where('lt_loan.status', 'A')
                      ->where('lt_loan.user_id', $id)
                      ->get();


          $ms_customer = DB::table('ms_customer')
                      ->where('ms_customer.status', 'A')
                      ->where('ms_customer.customer_id', $id)
                      ->first();

          return view('mainmenu/customer/custdetail')->with('customer', $ms_customer)
                                                    ->with('lt_piutang', $lt_piutang)
                                                    ->with('sellings', $sellings)
                                                    ->with('lt_loan', $lt_loan);
        }

        // manage customer add function-----------------------------------------------------
        public function add_cust()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Customer';
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

            return view('mainmenu/customer/custadd');
        }
        // ---------------------------------------------------------------------------------

        // manage customer do add function--------------------------------------------------
        public function do_add_cust(Request $request)
        {
            $this->validate($request, [
                'cust_name'         => 'required|max:100',
                'cust_address'      => 'required',
                'email'             => 'required|email_valid|max:50|unique:ms_customer',
                'no_telp'           => 'required|max:15',
                'norek'             => 'required|max:50',
                'card_name'         => 'required|max:100',
                'bank_name'         => 'required|max:20'
            ]);

            // validate increment id
                $id = DB::table('ms_customer')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->customer_id,4,6);
                    $next_id    = $lastnumber + 1;
                    $id         = "CUST".sprintf("%06d", $next_id);
                }else{
                    $id         = "CUST000001";
                }
            // validate increment id

            $data = new manage_customer;
            $data->customer_id                  = $id;
            $data->nama                         = $request->cust_name;
            $data->alamat                       = $request->cust_address;
            $data->email                        = $request->email;
            $data->no_telp                      = $request->no_telp;
            $data->no_hp                        = $request->no_hp;
            $data->fax                          = $request->fax;
            $data->no_rekening                  = $request->norek;
            $data->nama_rekening                = $request->card_name;
            $data->bank_nama                    = $request->bank_name;
            $data->npwp                         = $request->npwp;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Customer';
                $table_logs  = 'ms_customer';
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

            $data_cust = new deposit_cust;
            $data_cust->customer_id                  = $id;
            $data_cust->deposit                      = 0;
            $data_cust->created_date                 = date('Y-m-d H:i:s');
            $data_cust->last_modify_date             = date('Y-m-d H:i:s');
            $data_cust->modify_user_id               = Auth::user()->karyawan_id;
            $data_cust->status                       = 'A';
            $data_cust->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Customer | Do Add New Deposit Customer';
                $table_logs  = 'tr_deposit_customer';
                $id_logs     = $data->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($data_cust);

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
                $do_logs     = 'Do Add New Customer | Do Add Customer Loan';
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

            return redirect(url('mainmenu/cust_home'))->with('status', ' Created new customer has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage customer edit function----------------------------------------------------
        public function edit_cust($id)
        {
           $ms_customer = DB::table('ms_customer')
                        ->where('ms_customer.status', 'A')
                        ->where('ms_customer.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Customer';
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

            return view('mainmenu/customer/custedit', ['ms_customer' => $ms_customer]);
        }
        // ---------------------------------------------------------------------------------

        // manage customer do edit function-------------------------------------------------
        public function do_edit_cust(Request $request, $id)
        {
            $this->validate($request, [
                'cust_name'         => 'required|max:100',
                'cust_address'      => 'required',
                'email'             => 'required|email_valid|max:50',
                'no_telp'           => 'required|max:15',
                'norek'             => 'required|max:50',
                'card_name'         => 'required|max:100',
                'bank_name'         => 'required|max:20'
            ]);

            $data = manage_customer::find($id);
            $data->nama                         = $request->cust_name;
            $data->alamat                       = $request->cust_address;
            $data->email                        = $request->email;
            $data->no_telp                      = $request->no_telp;
            $data->no_hp                        = $request->no_hp;
            $data->fax                          = $request->fax;
            $data->no_rekening                  = $request->norek;
            $data->nama_rekening                = $request->card_name;
            $data->bank_nama                    = $request->bank_name;
            $data->npwp                         = $request->npwp;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Customer';
                $table_logs  = 'ms_customer';
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

            return redirect(url('mainmenu/cust_home'))->with('status', ' Updated customer has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage customer delete function--------------------------------------------------
        public function delete_cust($id)
        {
            $data = manage_customer::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Customer';
                $table_logs  = 'ms_customer ';
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

            return redirect(url('mainmenu/cust_home'))->with('status', ' Deleted customer has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage customer range function---------------------------------------------------
        public function cust_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idcust;
            $name       = $request->name;
            $phone      = $request->phone;
            $address    = $request->address;
            $email      = $request->email;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $phone == "" &&  $address == "" && $email == ""){

                return redirect(url('mainmenu/supp_home'));

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
                $id_sc          = $id != "" ? "AND a.customer_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
                $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";
                $email_sc       = $email != "" ? "AND a.email LIKE '%".$email."%'" : "";

                // query range
                $ms_customer = DB::select("SELECT a.*
                                FROM ms_customer a
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $phone_sc $address_sc $email_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$phone,$id,$address,$email];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Supplier';
                    $table_logs  = 'ms_customer';
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
                return view('mainmenu/customer/custhome')->with('ms_customer',$ms_customer)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage customer export function--------------------------------------------------
        public function cust_export(Request $request)
        {
           //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idcust;
            $name       = $request->name;
            $phone      = $request->phone;
            $address    = $request->address;
            $email      = $request->email;

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
            $id_sc          = $id != "" ? "AND a.customer_id LIKE '%".$id."%'" : "";
            $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
            $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
            $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";
            $email_sc       = $email != "" ? "AND a.email LIKE '%".$email."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_customer = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.customer_id AS 'custid', a.nama AS 'name', a.alamat AS 'address', a.no_telp AS 'phone',
                            a.no_hp AS 'phone2', a.email AS 'email', a.fax AS 'fax', a.no_rekening AS 'accnumber',
                            a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname', a.npwp AS 'npwp'
                            FROM ms_customer a
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $name_sc $phone_sc $address_sc $email_sc
                            ORDER BY a.created_date DESC");

            }else{

                $ms_customer = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.customer_id AS 'custid', a.nama AS 'name', a.alamat AS 'address', a.no_telp AS 'phone',
                            a.no_hp AS 'phone2', a.email AS 'email', a.fax AS 'fax', a.no_rekening AS 'accnumber',
                            a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname', a.npwp AS 'npwp'
                            FROM ms_customer a
                            WHERE a.`status` = 'A'
                            $id_sc $name_sc $phone_sc $address_sc $email_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Customer - '.date("d-m-Y").'', function($result) use ($ms_customer, $date_start_format, $date_end_format) {

                $result->sheet('Data Customer', function($sheet) use($ms_customer,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_customer as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->custid,
                                $item->name,
                                $item->address,
                                $item->phone,
                                $item->phone2,
                                $item->email,
                                $item->fax,
                                $item->accnumber,
                                $item->cardname,
                                $item->bankname,
                                $item->npwp
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','CUSTOMER REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Customer ID','Name','Address','Telephone', 'Handphone', 'Email', 'Fax', 'Account Number', 'Card Name', 'Bank Name', 'NPWP'));
                    $sheet->setBorder('A9:L9', 'thin');

                    // set style column


                    $sheet->cells('A9:L9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:L1');
                    $sheet->cells('A1:L1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':L'.$k, 'thin');
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

            $arraydate = [$start,$end,$name,$phone,$id,$address,$email];

            // return to view
            return view('mainmenu/customer/custhome', ['ms_customer' => $ms_customer, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage customer-----------------------------------------------------------------
}
