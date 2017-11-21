<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_supplier;
use App\Models\deposit_supp;
use App\Models\manage_loan;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Supplier extends Controller
{
	function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage supplier-----------------------------------------------------------------

        // manage supplier index function---------------------------------------------------
        public function index_supp()
        {
            $ms_supplier = DB::table('ms_supplier')
                        ->where('ms_supplier.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Supplier';
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
            return view('mainmenu/supplier/supphome', ['ms_supplier' => $ms_supplier]);
        }
        // ---------------------------------------------------------------------------------

				public function detail_supp($id)
        {
          // query left join

					$buyings = DB::select("SELECT a.*, b.status_hutang AS 'status_hutang', c.supplier_id AS 'suppid', c.nama AS 'namasupplier'
													FROM ms_pembelian a LEFT JOIN lt_hutang b
													ON a.id = b.pembelian_id
													LEFT JOIN ms_supplier c
													ON a.supplier_id = c.supplier_id
													WHERE a.status = 'A' AND a.supplier_id = '$id'");

					$lt_hutang = DB::select("SELECT a.*, b.pembelian_id AS 'b_pembelian_id', b.tanggal_jatuh_tempo_pembelian AS 'jatuhtempo', b.modify_user_id AS 'modify_user_id'
							FROM lt_hutang a LEFT JOIN ms_pembelian b
							ON a.pembelian_id = b.id
							WHERE a.status = 'A'
							AND a.status_hutang <>'2'
							AND b.supplier_id = '$id'");

          $lt_loan = DB::table('lt_loan')
											->select('lt_loan.*','ms_supplier.nama AS supplier_name')
											->leftJoin('ms_supplier', 'lt_loan.user_id', '=', 'ms_supplier.supplier_id')
											->leftJoin('lt_biayalain', 'lt_loan.user_id', '=', 'lt_biayalain.biayalain_id')
                      ->where('lt_loan.status_loan', '<>', 2)
                      ->where('lt_loan.status', 'A')
                      ->where('lt_loan.user_id', $id)
                      ->get();


          $ms_supplier = DB::table('ms_supplier')
                      ->where('ms_supplier.status', 'A')
                      ->where('ms_supplier.supplier_id', $id)
                      ->first();

          return view('mainmenu/supplier/suppdetail')->with('supplier', $ms_supplier)
                                                    ->with('lt_hutang', $lt_hutang)
                                                    ->with('buyings', $buyings)
                                                    ->with('lt_loan', $lt_loan);
        }

        // manage supplier add function-----------------------------------------------------
        public function add_supp()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Supplier';
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

            return view('mainmenu/supplier/suppadd');
        }
        // ---------------------------------------------------------------------------------

        // manage supplier do add function--------------------------------------------------
        public function do_add_supp(Request $request)
        {
            $this->validate($request, [
                'supp_name'         => 'required|max:100',
                'supp_address'      => 'required',
                'email'             => 'required|email_valid|max:50|unique:ms_supplier',
                'no_telp'           => 'required|max:15',
                'norek'             => 'required|max:50',
                'card_name'         => 'required|max:100',
                'bank_name'         => 'required|max:20'
            ]);

            // validate increment id
                $id = DB::table('ms_supplier')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->supplier_id,4,6);
                    $next_id    = $lastnumber + 1;
                    $id         = "SUPP".sprintf("%06d", $next_id);
                }else{
                    $id         = "SUPP000001";
                }
            // validate increment id

            $data = new manage_supplier;
            $data->supplier_id                  = $id;
            $data->nama                         = $request->supp_name;
            $data->alamat                       = $request->supp_address;
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
                $do_logs     = 'Do Add New Supplier';
                $table_logs  = 'ms_supplier';
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

            $data_supp = new deposit_supp;
            $data_supp->supplier_id                  = $id;
            $data_supp->deposit                      = 0;
            $data_supp->created_date                 = date('Y-m-d H:i:s');
            $data_supp->last_modify_date             = date('Y-m-d H:i:s');
            $data_supp->modify_user_id               = Auth::user()->karyawan_id;
            $data_supp->status                       = 'A';
            $data_supp->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Supplier | Do Add New Deposit Supplier';
                $table_logs  = 'tr_deposit_supplier';
                $id_logs     = $data->id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($data_supp);

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
                $do_logs     = 'Do Add New Supplier | Do Add Supplier Loan';
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

            return redirect(url('mainmenu/supp_home'))->with('status', ' Created new supplier has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage supplier edit function----------------------------------------------------
        public function edit_supp($id)
        {
           $ms_supplier = DB::table('ms_supplier')
                        ->where('ms_supplier.status', 'A')
                        ->where('ms_supplier.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Supplier';
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

            return view('mainmenu/supplier/suppedit', ['ms_supplier' => $ms_supplier]);
        }
        // ---------------------------------------------------------------------------------

        // manage supplier do edit function-------------------------------------------------
        public function do_edit_supp(Request $request, $id)
        {
            $this->validate($request, [
                'supp_name'         => 'required|max:100',
                'supp_address'      => 'required',
                'email'             => 'required|email_valid|max:50',
                'no_telp'           => 'required|max:15',
                'norek'             => 'required|max:50',
                'card_name'         => 'required|max:100',
                'bank_name'         => 'required|max:20'
            ]);

            $data = manage_supplier::find($id);
            $data->nama                         = $request->supp_name;
            $data->alamat                       = $request->supp_address;
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
                $do_logs     = 'Do Edit New Supplier';
                $table_logs  = 'ms_supplier';
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

            return redirect(url('mainmenu/supp_home'))->with('status', ' Updated supplier has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage supplier delete function--------------------------------------------------
        public function delete_supp($id)
        {
            $data = manage_supplier::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Supplier';
                $table_logs  = 'ms_supplier ';
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

            return redirect(url('mainmenu/supp_home'))->with('status', ' Deleted supplier has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage supplier range function---------------------------------------------------
        public function supp_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idsupp;
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
                $id_sc          = $id != "" ? "AND a.supplier_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
                $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";
                $email_sc       = $email != "" ? "AND a.email LIKE '%".$email."%'" : "";

                // query range
                $ms_supplier = DB::select("SELECT a.*
                                FROM ms_supplier a
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $phone_sc $address_sc $email_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$phone,$id,$address,$email];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Supplier';
                    $table_logs  = 'ms_supplier';
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
                return view('mainmenu/supplier/supphome')->with('ms_supplier',$ms_supplier)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage supplier export function--------------------------------------------------
        public function supp_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idsupp;
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
            $id_sc          = $id != "" ? "AND a.supplier_id LIKE '%".$id."%'" : "";
            $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
            $phone_sc       = $phone != "" ? "AND a.no_telp LIKE '%".$phone."%'" : "";
            $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";
            $email_sc       = $email != "" ? "AND a.email LIKE '%".$email."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_supplier = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.supplier_id AS 'suppid', a.nama AS 'name', a.alamat AS 'address', a.no_telp AS 'phone',
                            a.no_hp AS 'phone2', a.email AS 'email', a.fax AS 'fax', a.no_rekening AS 'accnumber',
                            a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname', a.npwp AS 'npwp'
                            FROM ms_supplier a
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $name_sc $phone_sc $address_sc $email_sc
                            ORDER BY a.created_date DESC");

            }else{

                $ms_supplier = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.supplier_id AS 'suppid', a.nama AS 'name', a.alamat AS 'address', a.no_telp AS 'phone',
                            a.no_hp AS 'phone2', a.email AS 'email', a.fax AS 'fax', a.no_rekening AS 'accnumber',
                            a.nama_rekening AS 'cardname', a.bank_nama AS 'bankname', a.npwp AS 'npwp'
                            FROM ms_supplier a
                            WHERE a.`status` = 'A'
                            $id_sc $name_sc $phone_sc $address_sc $email_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Supplier - '.date("d-m-Y").'', function($result) use ($ms_supplier, $date_start_format, $date_end_format) {

                $result->sheet('Data Supplier', function($sheet) use($ms_supplier,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_supplier as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->suppid,
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
                    $sheet->row(4, array('','SUPPLIER REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Supplier ID','Name','Address','Telephone', 'Handphone', 'Email', 'Fax', 'Account Number', 'Card Name', 'Bank Name', 'NPWP'));
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
            return view('mainmenu/supplier/supphome', ['ms_supplier' => $ms_supplier, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage supplier-----------------------------------------------------------------
}
