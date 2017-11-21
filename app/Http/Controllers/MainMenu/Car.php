<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_car;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Car extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage car----------------------------------------------------------------------

        // manage car index function--------------------------------------------------------
        public function index_car()
        {
            $ms_mobil = DB::table('ms_mobil')
                        ->select('ms_mobil.*', 'ms_mobil.mobil_id AS car_id' , 'ms_customer.nama AS customer_name', 'ms_karyawan.nama AS employee_name')
                        ->leftJoin('ms_customer', 'ms_mobil.customer_id', '=', 'ms_customer.customer_id')
                        ->leftJoin('ms_karyawan', 'ms_mobil.customer_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('ms_mobil.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Car';
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
            return view('mainmenu/car/carhome', ['ms_mobil' => $ms_mobil]);
        }
        // ---------------------------------------------------------------------------------

        public function detail_car($id)
        {
          $ms_mobil = DB::table('ms_mobil')
                      ->select('ms_mobil.*', 'ms_mobil.mobil_id AS car_id' , 'ms_customer.nama AS customer_name', 'ms_karyawan.nama AS employee_name')
                      ->leftJoin('ms_customer', 'ms_mobil.customer_id', '=', 'ms_customer.customer_id')
                      ->leftJoin('ms_karyawan', 'ms_mobil.customer_id', '=', 'ms_karyawan.karyawan_id')
                      ->where('ms_mobil.status', 'A')
                      ->where('ms_mobil.mobil_id', $id)
                      ->first();

          // return to view
          return view('mainmenu/car/cardetail', ['ms_mobil' => $ms_mobil]);
        }

        // manage car select2 customer/employee function------------------------------------
        public function search_cust()
        {
            $term = strip_tags(trim($_GET['q']));
            $typeterm = strip_tags(trim($_GET['j']));

            if($typeterm == 1){
                $ms_karyawan = DB::table('ms_karyawan')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('karyawan_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_karyawan;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->karyawan_id));
                        $new_row['text']=htmlentities(stripslashes($row->karyawan_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }

            }else if($typeterm == 2){
                $ms_customer = DB::table('ms_customer')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('customer_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_customer;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->customer_id));
                        $new_row['text']=htmlentities(stripslashes($row->customer_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }
            }
        }
        // ---------------------------------------------------------------------------------

        // manage car add function----------------------------------------------------------
        public function add_car()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Car';
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

            return view('mainmenu/car/caradd');
        }
        // ---------------------------------------------------------------------------------

        // manage car do add function-------------------------------------------------------
        public function do_add_car(Request $request)
        {
            $this->validate($request, [
                'type'              => 'required|min:1',
                'cust_id'           => 'required|max:20',
                'car_address'       => 'required',
                'car_brand'         => 'required|max:50',
                'car_type'          => 'required|max:50',
                'car_group'         => 'required|max:50',
                'car_model'         => 'required|max:50'
            ]);

            // validate increment id
                $id = DB::table('ms_mobil')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->mobil_id,3,7);
                    $next_id    = $lastnumber + 1;
                    $id         = "CAR".sprintf("%07d", $next_id);
                }else{
                    $id         = "CAR0000001";
                }
            // validate increment id

            $data = new manage_car;
            $data->type_car                     = $request->type;
            $data->mobil_id                     = $id;
            $data->customer_id                  = $request->cust_id;
            $data->no_polisi_mobil              = $request->car_nopol;
            $data->alamat_pemilik               = $request->car_address;
            $data->merek_mobil                  = $request->car_brand;
            $data->tipe_mobil                   = $request->car_type;
            $data->jenis_mobil                  = $request->car_group;
            $data->model                        = $request->car_model;
            $data->tahun_pembuatan_mobil        = $request->car_prod;
            $data->warna_mobil                  = $request->car_color;
            $data->no_rangka_mobil              = $request->no_chassis;
            $data->isi_silinder_mobil           = $request->silinder;
            $data->bahan_bakar_mobil            = $request->fuel;
            $data->no_bpkb_mobil                = $request->bpkb;
            $data->tahun_registrasi_mobil       = $request->reg_date;
            $data->indent_mobil                 = $request->indent;
            $data->status_mobil                 = $request->status_car;
            $data->no_mesin_mobil               = $request->machine_car;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Car';
                $table_logs  = 'ms_mobil';
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

            return redirect(url('mainmenu/car_home'))->with('status', ' Add new car has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage car edit function---------------------------------------------------------
        public function edit_car($id)
        {
           $ms_mobil = DB::table('ms_mobil')
                         ->select('ms_mobil.*', 'ms_customer.nama AS name_customer', 'ms_karyawan.nama AS name_employee')
                        ->leftJoin('ms_customer', 'ms_mobil.customer_id', '=', 'ms_customer.customer_id')
                        ->leftJoin('ms_karyawan', 'ms_mobil.customer_id', '=', 'ms_karyawan.karyawan_id')
                        ->where('ms_mobil.status', 'A')
                        ->where('ms_mobil.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Car';
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

            return view('mainmenu/car/caredit', ['ms_mobil' => $ms_mobil]);
        }
        // ---------------------------------------------------------------------------------

        // manage car do edit function------------------------------------------------------
        public function do_edit_car(Request $request, $id)
        {
            $this->validate($request, [
                'cust_id'           => 'required|max:20',
                'car_address'       => 'required',
                'car_brand'         => 'required|max:50',
                'car_type'          => 'required|max:50',
                'car_group'         => 'required|max:50',
                'car_model'         => 'required|max:50'
            ]);

            $data = manage_car::find($id);
            $data->no_polisi_mobil              = $request->car_nopol;
            $data->alamat_pemilik               = $request->car_address;
            $data->merek_mobil                  = $request->car_brand;
            $data->tipe_mobil                   = $request->car_type;
            $data->jenis_mobil                  = $request->car_group;
            $data->model                        = $request->car_model;
            $data->tahun_pembuatan_mobil        = $request->car_prod;
            $data->warna_mobil                  = $request->car_color;
            $data->no_rangka_mobil              = $request->no_chassis;
            $data->isi_silinder_mobil           = $request->silinder;
            $data->bahan_bakar_mobil            = $request->fuel;
            $data->no_bpkb_mobil                = $request->bpkb;
            $data->tahun_registrasi_mobil       = $request->reg_date;
            $data->indent_mobil                 = $request->indent;
            $data->status_mobil                 = $request->status_car;
            $data->no_mesin_mobil               = $request->machine_car;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Car';
                $table_logs  = 'ms_mobil';
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

            return redirect(url('mainmenu/car_home'))->with('status', ' Updated car has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage car delete function-------------------------------------------------------
        public function delete_car($id)
        {
            $data = manage_car::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Car';
                $table_logs  = 'ms_car ';
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

            return redirect(url('mainmenu/car_home'))->with('status', ' Deleted car has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage car range function--------------------------------------------------------
        public function car_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idcar;
            $type       = $request->status;
            $address    = $request->address;

            // validate empty
            if($start == "" && $end == "" && $id == "" && $type == "" &&  $address == ""){

                return redirect(url('mainmenu/car_home'));

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
                $id_sc          = $id != "" ? "AND a.mobil_id LIKE '%".$id."%'" : "";
                $type_sc        = $type != "" ? "AND a.type_car = '".$type."'" : "";
                $address_sc     = $address != "" ? "AND a.alamat_pemilik LIKE '%".$address."%'" : "";

                // query range
                $ms_mobil = DB::select("SELECT a.*, a.mobil_id AS 'car_id',
                                b.nama AS 'employee_name', c.nama AS 'customer_name'
                                FROM ms_mobil a LEFT JOIN ms_karyawan b
                                ON a.customer_id = b.karyawan_id
                                LEFT JOIN ms_customer c
                                ON a.customer_id = c.customer_id
                                WHERE a.status = 'A'
                                $date_sc $id_sc $type_sc $address_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$id,$address,$type];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Car';
                    $table_logs  = 'ms_mobil';
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
                return view('mainmenu/car/carhome')->with('ms_mobil',$ms_mobil)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage car export function-------------------------------------------------------
        public function car_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idcar;
            $type       = $request->status;
            $address    = $request->address;

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
            $id_sc          = $id != "" ? "AND a.mobil_id LIKE '%".$id."%'" : "";
            $type_sc        = $type != "" ? "AND a.type_car = '".$type."'" : "";
            $address_sc     = $address != "" ? "AND a.alamat_pemilik LIKE '%".$address."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_mobil = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.type_car AS 'typecar',
                            a.mobil_id AS 'carid', a.customer_id AS 'custid', a.no_polisi_mobil AS 'nopol',
                            a.alamat_pemilik AS 'address', a.merek_mobil AS 'merk', a.tipe_mobil AS 'type',
                            a.jenis_mobil AS 'catcar', a.model AS 'model', a.tahun_pembuatan_mobil AS 'year',
                            a.warna_mobil AS 'color', a.no_rangka_mobil AS 'chassis', a.isi_silinder_mobil AS 'silinder',
                            a.bahan_bakar_mobil AS 'fuel', a.no_bpkb_mobil AS 'bpkb', a.tahun_registrasi_mobil AS 'regyear',
                            a.indent_mobil AS 'indent', a.status_mobil AS 'status', a.no_mesin_mobil AS 'machine',
                            b.nama AS 'employeename', c.nama AS 'customername'
                            FROM ms_mobil a LEFT JOIN ms_karyawan b
                            ON a.customer_id = b.karyawan_id
                            LEFT JOIN ms_customer c
                            ON a.customer_id = c.customer_id
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $type_sc $address_sc
                            ORDER BY a.created_date DESC");

            }else{

                $ms_mobil = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.type_car AS 'typecar',
                            a.mobil_id AS 'carid', a.customer_id AS 'custid', a.no_polisi_mobil AS 'nopol',
                            a.alamat_pemilik AS 'address', a.merek_mobil AS 'merk', a.tipe_mobil AS 'type',
                            a.jenis_mobil AS 'catcar', a.model AS 'model', a.tahun_pembuatan_mobil AS 'year',
                            a.warna_mobil AS 'color', a.no_rangka_mobil AS 'chassis', a.isi_silinder_mobil AS 'silinder',
                            a.bahan_bakar_mobil AS 'fuel', a.no_bpkb_mobil AS 'bpkb', a.tahun_registrasi_mobil AS 'regyear',
                            a.indent_mobil AS 'indent', a.status_mobil AS 'status', a.no_mesin_mobil AS 'machine',
                            b.nama AS 'employeename', c.nama AS 'customername'
                            FROM ms_mobil a LEFT JOIN ms_karyawan b
                            ON a.customer_id = b.karyawan_id
                            LEFT JOIN ms_customer c
                            ON a.customer_id = c.customer_id
                            WHERE a.`status` = 'A'
                            $id_sc $type_sc $address_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Car - '.date("d-m-Y").'', function($result) use ($ms_mobil, $date_start_format, $date_end_format) {

                $result->sheet('Data Car', function($sheet) use($ms_mobil,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_mobil as $item){

                        if($item->typecar != 1){
                            $owner = $item->customername;
                        }else{
                            $owner = $item->employeename;
                        }

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->typecar,
                                $item->carid,
                                $owner,
                                $item->nopol,
                                $item->address,
                                $item->merk,
                                $item->type,
                                $item->catcar,
                                $item->model,
                                $item->year,
                                $item->color,
                                $item->chassis,
                                $item->silinder,
                                $item->fuel,
                                $item->bpkb,
                                $item->regyear,
                                $item->indent,
                                $item->status,
                                $item->machine
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','CAR REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Type','Car ID','Owner','No Policy', 'Address', 'Brand', 'Car Type', 'Group', 'Model', 'Production Year', 'Color', 'Chassis Number', 'Cylinder', 'Fuel', 'BPKB', 'Registration Year', 'Indent', 'Status Car', 'Machine Number'));
                    $sheet->setBorder('A9:T9', 'thin');

                    // set style column


                    $sheet->cells('A9:T9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:T1');
                    $sheet->cells('A1:T1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':T'.$k, 'thin');
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

            $arraydate = [$start,$end,$id,$type,$address];

            // return to view
            return view('mainmenu/car/carhome', ['ms_mobil' => $ms_mobil, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage car----------------------------------------------------------------------
}
