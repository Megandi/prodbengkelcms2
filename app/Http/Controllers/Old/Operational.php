<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_items;
use App\Models\manage_supplier;
use App\Models\manage_customer;
use App\Models\manage_car;
use App\Models\manage_quarry;
use App\Models\manage_port;
use App\Models\manage_solar;
use App\Models\manage_houling;
use App\Models\manage_type_items;
use App\Models\manage_solar_type;
use App\Models\manage_route;
use App\Models\manage_tonase;
use App\Models\manage_service;

use App\Models\deposit_cust;
use App\Models\deposit_supp;

use App\Models\manage_loan;
use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Operational extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    // menu manage type items---------------------------------------------------------------

        // manage type items index function-------------------------------------------------
        public function index_items_type()
        {
            // query left join
            $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Type Items';
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
            return view('operational/type_items/typehome', ['ms_kategori_barang' => $ms_kategori_barang]);
        }
        // ---------------------------------------------------------------------------------

        // manage type items add function---------------------------------------------------
        public function add_items_type()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Type Items';
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

            return view('operational/type_items/typeadd');
        }
        // ---------------------------------------------------------------------------------

        // manage type items do add function------------------------------------------------
        public function do_add_items_type(Request $request)
        {
            $type = $request->type_date;

            if($type != 1){
                $this->validate($request, [
                    'name'          => 'required|max:100|unique:ms_kategori_barang',
                    'expiry'        => 'required'
                ]);
            }else{
                $this->validate($request, [
                    'name'          => 'required|max:100|unique:ms_kategori_barang',
                    'year'          => 'max:10',
                    'month'         => 'max:10',
                    'day'           => 'max:10'
                ]);
            }

            $data = new manage_type_items;
            $data->name             = $request->name;

            if($type != 1){
                $data->expiry_date  = $request->expiry;
                $data->year         = 0;
                $data->month        = 0;
                $data->day          = 0;
            }else{
                if($request->year != ""){
                    $data->year         = $request->year;
                }else{
                    $data->year         = 0;
                }
                if($request->month != ""){
                    $data->month        = $request->month;
                }else{
                    $data->month        = 0;
                }
                if($request->day != ""){
                    $data->day          = $request->day;
                }else{
                    $data->day          = 0;
                }

                // calculate interval date
                $date = date_create(date("Y/m/d"));
                date_add($date, date_interval_create_from_date_string(''.$request->year.' years'));
                date_add($date, date_interval_create_from_date_string(''.$request->month.' months'));
                date_add($date, date_interval_create_from_date_string(''.$request->day.' days'));
                $expiry = date_format($date, 'Y-m-d');
                // calculate interval date

                $data->expiry_date  = $expiry;
            }

            if($request->inven != null){
                $data->is_inventory = 1;
            }
            else{
                $data->is_inventory = 0;
            }

            $data->type_date        = $request->type_date;
            $data->created_date     = date('Y-m-d H:i:s');
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Type Items'; 
                $table_logs  = 'ms_kategori_barang';
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

            return redirect(url('operational/items_type_home'))->with('status', ' Created new type items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage type items edit function--------------------------------------------------
        public function edit_items_type($id)
        {
            $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Type Items';
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

            return view('operational/type_items/typeedit', ['ms_kategori_barang' => $ms_kategori_barang]);
        }
        // ---------------------------------------------------------------------------------

        // manage type items do edit function-----------------------------------------------
        public function do_edit_items_type(Request $request, $id)
        {
            $type = $request->type_date;

            if($type != 1){
                $this->validate($request, [
                    'name'          => 'required|max:100',
                    'expiry'        => 'required'
                ]);
            }else{
                $this->validate($request, [
                    'name'          => 'required|max:100',
                    'year'          => 'max:10',
                    'month'         => 'max:10',
                    'day'           => 'max:10'
                ]);
            }

            $data = manage_type_items::find($id);
            $data->name             = $request->name;

            if($type != 1){
                $data->expiry_date  = $request->expiry;
                $data->year         = 0;
                $data->month        = 0;
                $data->day          = 0;
            }else{
                if($request->year != ""){
                    $data->year         = $request->year;
                }else{
                    $data->year         = 0;
                }
                if($request->month != ""){
                    $data->month        = $request->month;
                }else{
                    $data->month        = 0;
                }
                if($request->day != ""){
                    $data->day          = $request->day;
                }else{
                    $data->day          = 0;
                }

                // calculate interval date
                $date = date_create(date("Y/m/d"));
                date_add($date, date_interval_create_from_date_string(''.$request->year.' years'));
                date_add($date, date_interval_create_from_date_string(''.$request->month.' months'));
                date_add($date, date_interval_create_from_date_string(''.$request->day.' days'));
                $expiry = date_format($date, 'Y-m-d');
                // calculate interval date

                $data->expiry_date  = $expiry;
            }

            if($request->inven != null){
                $data->is_inventory = 1;
            }
            else{
                $data->is_inventory = 0;
            }
            $data->type_date        = $request->type_date;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Type Items'; 
                $table_logs  = 'ms_kategori_barang';
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

            return redirect(url('operational/items_type_home'))->with('status', ' Updated type items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage type items delete function------------------------------------------------
        public function delete_items_type($id)
        {
            $data = manage_type_items::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->delete_date      = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Type Items'; 
                $table_logs  = 'ms_kategori_barang';
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

            return redirect(url('operational/items_type_home'))->with('status', ' Deleted type items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage type items range function------------------------------------------------
        public function items_type_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $name       = $request->name;
            $status     = $request->status;

            // validate empty
            if($start == "" && $end == "" && $name == "" && $status == ""){    
                
                return redirect(url('operational/items_type_home'));

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
                $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";
                $status_sc      = $status != "" ? "AND a.is_inventory = '".$status."'" : "";

                // query range
                $ms_kategori_barang = DB::select("SELECT a.*
                                FROM ms_kategori_barang a
                                WHERE a.status = 'A'
                                $date_sc $name_sc $status_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$status];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Type Items'; 
                    $table_logs  = 'ms_kategori_barang';
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
                return view('operational/type_items/typehome')->with('ms_kategori_barang',$ms_kategori_barang)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage type items export function----------------------------------------------------
        public function items_type_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $name       = $request->name;
            $status     = $request->status;

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
            $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";
            $status_sc      = $status != "" ? "AND a.is_inventory = '".$status."'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_kategori_barang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                    a.name AS 'name',
                    a.type_date AS 'typedate', a.expiry_date AS 'expiry', a.is_inventory AS 'inventory'
                    FROM ms_kategori_barang a
                    WHERE a.`status` = 'A'
                    $date_sc $name_sc $status_sc
                    ORDER BY a.created_date DESC");

            }else{

                $ms_kategori_barang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                    a.name AS 'name',
                    a.type_date AS 'typedate', a.expiry_date AS 'expiry', a.is_inventory AS 'inventory'
                    FROM ms_kategori_barang a 
                    WHERE a.`status` = 'A'
                    $name_sc $status_sc
                    ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Type Items - '.date("d-m-Y").'', function($result) use ($ms_kategori_barang, $date_start_format, $date_end_format) {

                $result->sheet('Data Type Items', function($sheet) use($ms_kategori_barang,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_kategori_barang as $item){

                        if($item->inventory != 1){
                            $inven = 'No';
                        }else{
                            $inven = 'Yes';
                        }

                        if($item->typedate != 1){
                            $timeage = 'Time';
                        }else{
                            $timeage = 'Age';
                        }

                        $expiry = date("d/m/Y",strtotime($item->expiry));
                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->name,
                                $timeage,
                                $expiry,
                                $inven
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','TYPE ITEMS REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Name','Type Date','Expiry Date','Inventory'));
                    $sheet->setBorder('A9:E9', 'thin');

                    // set style column


                    $sheet->cells('A9:E9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:E1');
                    $sheet->cells('A1:E1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':E'.$k, 'thin');
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

            $arraydate = [$start,$end,$name,$status];

            // return to view
            return view('operational/type_items/typehome', ['ms_kategori_barang' => $ms_kategori_barang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage type items---------------------------------------------------------------

    // menu manage items--------------------------------------------------------------------

        // manage items index function------------------------------------------------------
        public function index_items()
        {
            $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->get();

            // query left join
            $ms_barang = DB::table('ms_barang')
                        ->select('ms_barang.*', 'ms_barang.barang_id AS items_id', 'ms_kategori_barang.name AS category_name')
                        ->leftJoin('ms_kategori_barang', 'ms_barang.kategori', '=', 'ms_kategori_barang.id')
                        ->where('ms_barang.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Items';
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
            return view('operational/items/itemshome', ['ms_barang' => $ms_barang, 'ms_kategori_barang' => $ms_kategori_barang]);
        }
        // ---------------------------------------------------------------------------------

        // manage items add function--------------------------------------------------------
        public function add_items()
        {
            $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Type Items';
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

            return view('operational/items/itemsadd',['ms_kategori_barang'=>$ms_kategori_barang]);
        }
        // ---------------------------------------------------------------------------------

        // manage items do add function-----------------------------------------------------
        public function do_add_items(Request $request)
        {
            $this->validate($request, [
                'items_name'        => 'required|max:500',
                'category_name'     => 'required|max:10',
                'spec'              => 'max:500'
            ]);

            // validate increment id
                $id = DB::table('ms_barang')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->barang_id,2,8);
                    $next_id    = $lastnumber + 1;
                    $id         = "BR".sprintf("%08d", $next_id);
                }else{
                    $id         = "BR00000001";
                }
            // validate increment id

            $data = new manage_items;
            $data->barang_id                    = $id;
            $data->nama                         = $request->items_name;
            $data->kategori                     = $request->category_name;
            $data->spesifikasi                  = $request->spec;
            $data->harga                        = $request->price;
            $data->harga_jual                   = $request->price_sell;
            $data->stock                        = $request->stock;
            $data->is_available                 = 0;

            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Items'; 
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

            return redirect(url('operational/items_home'))->with('status', ' Created new items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage items edit function-------------------------------------------------------
        public function edit_items($id)
        {
            $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->get();

            $ms_barang = DB::table('ms_barang')
                        ->select('ms_barang.*', 'ms_barang.barang_id AS items_id', 'ms_kategori_barang.name AS category_name')
                        ->leftJoin('ms_kategori_barang', 'ms_barang.kategori', '=', 'ms_kategori_barang.id')
                        ->where('ms_barang.status', 'A')
                        ->where('ms_barang.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Items';
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

            return view('operational/items/itemsedit', ['ms_barang' => $ms_barang, 'ms_kategori_barang' => $ms_kategori_barang]);
        }
        // ---------------------------------------------------------------------------------

        // manage items do edit function----------------------------------------------------
        public function do_edit_items(Request $request, $id)
        {
            $this->validate($request, [
                'items_name'        => 'required|max:500',
                'category_name'     => 'required|max:10',
                'spec'              => 'max:500'
            ]);

            $data = manage_items::find($id);
            $data->nama                         = $request->items_name;
            $data->kategori                     = $request->category_name;
            $data->spesifikasi                  = $request->spec;
            $data->harga_jual                   = $request->price_sell;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Items'; 
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

            return redirect(url('operational/items_home'))->with('status', ' Updated items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage items delete function-----------------------------------------------------
        public function delete_items($id)
        {
            $data = manage_items::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Items'; 
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

            return redirect(url('operational/items_home'))->with('status', ' Deleted items has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage items range function------------------------------------------------------
        public function items_range(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $id             = $request->iditems;
            $name           = $request->name;
            $category       = $request->category;
            $spec           = $request->spec;
            $statusitems    = $request->status;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $category == "" &&  $spec == "" && $statusitems == ""){    
                return redirect(url('operational/items_home'));

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
                $id_sc          = $id != "" ? "AND a.barang_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $category_sc    = $category != "" ? "AND a.kategori = '".$category."'" : "";
                $spec_sc        = $spec != "" ? "AND a.spesifikasi LIKE '%".$spec."%'" : "";
                $statusitems_sc = $statusitems != "" ? "AND a.is_available = '".$statusitems."'" : "";

                // query range
                $ms_barang = DB::select("SELECT a.*, a.barang_id AS 'items_id', b.name AS 'category_name'
                                FROM ms_barang a LEFT JOIN ms_kategori_barang b
                                ON a.kategori = b.id
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $category_sc $spec_sc $statusitems_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$id,$name,$category,$spec,$statusitems];

                // load category
                $ms_kategori_barang = DB::table('ms_kategori_barang')
                        ->where('status', 'A')
                        ->get();

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Items'; 
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

                // return to view
                return view('operational/items/itemshome')->with('ms_barang',$ms_barang)->with('arraydate',$arraydate)->with('ms_kategori_barang',$ms_kategori_barang);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage items export function------------------------------------------------------
        public function items_export(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $id             = $request->iditems;
            $name           = $request->name;
            $category       = $request->category;
            $spec           = $request->spec;
            $statusitems    = $request->status;

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
            $id_sc          = $id != "" ? "AND a.barang_id LIKE '%".$id."%'" : "";
            $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
            $category_sc    = $category != "" ? "AND a.kategori = '".$category."'" : "";
            $spec_sc        = $spec != "" ? "AND a.spesifikasi LIKE '%".$spec."%'" : "";
            $statusitems_sc = $statusitems != "" ? "AND a.is_available = '".$statusitems."'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_barang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.barang_id AS 'itemsid', a.nama AS 'name', b.name AS 'catname', a.spesifikasi AS 'spec',
                    a.harga AS 'subprice', a.stock AS 'stock', a.harga_jual AS 'sellprice', a.is_available AS 'statusitems'
                    FROM ms_barang a LEFT JOIN ms_kategori_barang b
                    ON a.kategori = b.id
                    WHERE a.status = 'A'
                    $date_sc $id_sc $name_sc $category_sc $spec_sc $statusitems_sc
                    ORDER BY a.created_date DESC");

            }else{

                $ms_barang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.barang_id AS 'itemsid', a.nama AS 'name', b.name AS 'catname', a.spesifikasi AS 'spec',
                    a.harga AS 'subprice', a.stock AS 'stock', a.harga_jual AS 'sellprice', a.is_available AS 'statusitems'
                    FROM ms_barang a LEFT JOIN ms_kategori_barang b
                    ON a.kategori = b.id
                    WHERE a.status = 'A'
                    $id_sc $name_sc $category_sc $spec_sc $statusitems_sc
                    ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Items - '.date("d-m-Y").'', function($result) use ($ms_barang, $date_start_format, $date_end_format) {

                $result->sheet('Data Items', function($sheet) use($ms_barang,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $totalbuy = 0;
                    $count = 0;
                    foreach($ms_barang as $item){

                        if($item->statusitems != 0){
                            $itemcond = 'Request';
                        }
                        else if($item->statusitems != 1){
                            $itemcond = 'Available';
                        }
                        else if($item->statusitems != 2){
                            $itemcond = 'On Service';
                        }

                        $totalbuy = $item->subprice * $item->stock;

                        $subprice = number_format($item->subprice);
                        $totalbuy = number_format($totalbuy);
                        $sellprice = number_format($item->sellprice);
                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->itemsid,
                                $item->name,
                                $item->catname,
                                $item->spec,
                                $subprice,
                                $item->stock,
                                $totalbuy,
                                $sellprice,
                                $itemcond
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','ITEMS REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','ID','Name','Category','Spesification', 'Sub Price Buying', 'Stock', 'Grand Total', 'Sub Price Selling', 'Item'));
                    $sheet->setBorder('A9:J9', 'thin');

                    // set style column


                    $sheet->cells('A9:J9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:J1');
                    $sheet->cells('A1:J1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':J'.$k, 'thin');
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

            $arraydate = [$start,$end,$id,$name,$category,$spec,$statusitems];

            // return to view
            return view('operational/items/itemshome', ['ms_barang' => $ms_barang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage items--------------------------------------------------------------------

    // menu manage service------------------------------------------------------------------

        // manage service index function----------------------------------------------------
        public function index_service()
        {
            // query left join
            $ms_jasa = DB::table('ms_jasa')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Service';
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
            return view('operational/service/servicehome', ['ms_jasa' => $ms_jasa]);
        }
        // ---------------------------------------------------------------------------------

        // manage service select2 items function--------------------------------------------
        public function search_items_service()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_barang = DB::select('SELECT * FROM ms_barang
                                    WHERE (status = "A"
                                    AND stock > 0 AND is_available = 1 AND nama like "%'.$term.'%")
                                    OR
                                    (status = "A"
                                    AND stock > 0 AND is_available = 1 AND barang_id like "%'.$term.'%")');
            $query = $ms_barang;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->barang_id));
                    $new_row['text']=htmlentities(stripslashes($row->barang_id ." - ". $row->nama ." - ". date('d/m/Y', strtotime($row->created_date))));
                    $new_row['name']=htmlentities(stripslashes($row->nama));
                    $new_row['sub_total']=htmlentities(stripslashes($row->harga_jual));
                    $new_row['qty']=htmlentities(stripslashes($row->stock));
                    $new_row['id_barang']=htmlentities(stripslashes($row->id));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage service add function------------------------------------------------------
        public function add_service()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Service';
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

            return view('operational/service/serviceadd');
        }
        // ---------------------------------------------------------------------------------

        // manage service do add function---------------------------------------------------
        public function do_add_service(Request $request)
        {
            $this->validate($request, [
                'name'        => 'required|max:500',
                'items_id'    => 'required|max:20',
                'items_name'  => 'required|max:500',
                'items_qty'   => 'max:10',
                'price'       => 'required|max:20'
            ]);

            // validate increment id
                $id = DB::table('ms_jasa')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->service_id,2,8);
                    $next_id    = $lastnumber + 1;
                    $id         = "SV".sprintf("%08d", $next_id);
                }else{
                    $id         = "SV00000001";
                }
            // validate increment id

            $data = new manage_service;
            $data->service_id             = $id;
            $data->barang_id              = $request->items_id;
            $data->barang_name            = $request->items_name;
            $data->name                   = $request->name;
            $data->price                  = $request->price;
            $data->qty                    = $request->items_qty;
            $data->created_date           = date('Y-m-d H:i:s');
            $data->last_modify_date       = date('Y-m-d H:i:s');
            $data->modify_user_id         = Auth::user()->karyawan_id;
            $data->status                 = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Service'; 
                $table_logs  = 'ms_jasa';
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

            $data_items = manage_items::where('id',$request->items_id_barang)->update([
                'stock' => 0,
                'is_available' => 2,
                'last_modify_date' => date('Y-m-d H:i:s'),
                'modify_user_id' => Auth::user()->karyawan_id,
                'status' => 'A'
            ]);

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Service | Update Items Stock'; 
                $table_logs  = 'ms_barang';
                $id_logs     = $id;
                $url_logs    = url()->current();
                $ip_logs     = $_SERVER['REMOTE_ADDR'];
                $param_logs  = json_encode($data_items);

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

            return redirect(url('operational/service_home'))->with('status', ' Created new service has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage service edit function-----------------------------------------------------
        public function edit_service($id)
        {

            $ms_jasa = DB::table('ms_jasa')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Service';
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

            return view('operational/service/serviceedit', ['ms_jasa' => $ms_jasa]);
        }
        // ---------------------------------------------------------------------------------

        // manage service do edit function--------------------------------------------------
        public function do_edit_service(Request $request, $id, $id_barang)
        {
       
            $this->validate($request, [
                'name'        => 'required|max:500'
            ]);

            $data = manage_service::find($id);
            $data->name                   = $request->name;
            $data->last_modify_date       = date('Y-m-d H:i:s');
            $data->modify_user_id         = Auth::user()->karyawan_id;
            $data->status                 = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Service'; 
                $table_logs  = 'ms_jasa';
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

            return redirect(url('operational/service_home'))->with('status', ' Updated service has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage service delete function---------------------------------------------------
        public function delete_service($id)
        {
            $data = manage_service::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Service'; 
                $table_logs  = 'ms_jasa ';
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

            return redirect(url('operational/service_home'))->with('status', ' Deleted service has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage service range function----------------------------------------------------
        public function service_range(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $iditems        = $request->iditems;
            $name           = $request->name;
            $idservice      = $request->idservice;
            $nameitems      = $request->nameitems;

            // validate empty
            if($start == "" && $end == "" &&  $iditems == "" &&  $name == "" &&  $idservice == "" && $nameitems == ""){    
                return redirect(url('operational/service_home'));

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
                $iditems_sc     = $iditems != "" ? "AND a.barang_id LIKE '%".$iditems."%'" : "";
                $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";
                $idservice_sc   = $idservice != "" ? "AND a.service_id LIKE '%".$idservice."%'" : "";
                $nameitems_sc   = $nameitems != "" ? "AND a.barang_name LIKE '%".$nameitems."%'" : "";

                // query range
                $ms_jasa = DB::select("SELECT a.* FROM ms_jasa a
                            WHERE a.`status` = 'A'
                            $date_sc $iditems_sc $name_sc $idservice_sc $nameitems_sc
                            ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$iditems,$name,$idservice,$nameitems];


                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Service'; 
                    $table_logs  = 'ms_jasa';
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
                return view('operational/service/servicehome')->with('ms_jasa',$ms_jasa)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage items export function------------------------------------------------------
        public function service_export(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $iditems        = $request->iditems;
            $name           = $request->name;
            $idservice      = $request->idservice;
            $nameitems      = $request->nameitems;

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
            $iditems_sc     = $iditems != "" ? "AND a.barang_id LIKE '%".$iditems."%'" : "";
            $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";
            $idservice_sc   = $idservice != "" ? "AND a.service_id LIKE '%".$idservice."%'" : "";
            $nameitems_sc   = $nameitems != "" ? "AND a.barang_name LIKE '%".$nameitems."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_jasa = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.service_id AS 'serviceid', a.barang_id AS 'itemsid', a.barang_name AS 'nameitems', a.name AS 'name', a.price AS 'price',
                    a.qty AS 'qty'
                    FROM ms_jasa a
                    WHERE a.status = 'A'
                    $date_sc $iditems_sc $name_sc $idservice_sc $nameitems_sc
                    ORDER BY a.created_date DESC");

            }else{

                $ms_jasa = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.service_id AS 'serviceid', a.barang_id AS 'itemsid', a.barang_name AS 'nameitems',
                    a.name AS 'name', a.price AS 'price', a.qty AS 'qty'
                    FROM ms_jasa a
                    WHERE a.status = 'A'
                    $iditems_sc $name_sc $idservice_sc $nameitems_sc
                    ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Service - '.date("d-m-Y").'', function($result) use ($ms_jasa, $date_start_format, $date_end_format) {

                $result->sheet('Data Service', function($sheet) use($ms_jasa,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_jasa as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->serviceid,
                                $item->itemsid,
                                $item->nameitems,
                                $item->name,
                                $item->price,
                                $item->qty
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SERVICE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Service ID','Items ID','Items Name','Name Service', 'Price', 'Quantities'));
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

            $arraydate = [$start,$end,$iditems,$name,$idservice,$nameitems];

            // return to view
            return view('operational/service/servicehome', ['ms_jasa' => $ms_jasa, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage service------------------------------------------------------------------

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
            return view('operational/supplier/supphome', ['ms_supplier' => $ms_supplier]);
        }
        // ---------------------------------------------------------------------------------

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

            return view('operational/supplier/suppadd');
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

            return redirect(url('operational/supp_home'))->with('status', ' Created new supplier has been success.');
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

            return view('operational/supplier/suppedit', ['ms_supplier' => $ms_supplier]);
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

            return redirect(url('operational/supp_home'))->with('status', ' Updated supplier has been success.');
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

            return redirect(url('operational/supp_home'))->with('status', ' Deleted supplier has been success.');
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
               
                return redirect(url('operational/supp_home'));

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
                return view('operational/supplier/supphome')->with('ms_supplier',$ms_supplier)->with('arraydate',$arraydate);
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
            return view('operational/supplier/supphome', ['ms_supplier' => $ms_supplier, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage supplier-----------------------------------------------------------------

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
            return view('operational/customer/custhome', ['ms_customer' => $ms_customer]);
        }
        // ---------------------------------------------------------------------------------

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

            return view('operational/customer/custadd');
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

            return redirect(url('operational/cust_home'))->with('status', ' Created new customer has been success.');
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

            return view('operational/customer/custedit', ['ms_customer' => $ms_customer]);
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

            return redirect(url('operational/cust_home'))->with('status', ' Updated customer has been success.');
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

            return redirect(url('operational/cust_home'))->with('status', ' Deleted customer has been success.');
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
               
                return redirect(url('operational/supp_home'));

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
                return view('operational/customer/custhome')->with('ms_customer',$ms_customer)->with('arraydate',$arraydate);
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
            return view('operational/customer/custhome', ['ms_customer' => $ms_customer, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage customer-----------------------------------------------------------------

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
            return view('operational/car/carhome', ['ms_mobil' => $ms_mobil]);
        }
        // ---------------------------------------------------------------------------------

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

            return view('operational/car/caradd');
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

            return redirect(url('operational/car_home'))->with('status', ' Add new car has been success.');
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

            return view('operational/car/caredit', ['ms_mobil' => $ms_mobil]);
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

            return redirect(url('operational/car_home'))->with('status', ' Updated car has been success.');
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

            return redirect(url('operational/car_home'))->with('status', ' Deleted car has been success.');
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
                
                return redirect(url('operational/car_home'));

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
                return view('operational/car/carhome')->with('ms_mobil',$ms_mobil)->with('arraydate',$arraydate);
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
            return view('operational/car/carhome', ['ms_mobil' => $ms_mobil, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage car----------------------------------------------------------------------

    // menu manage quar---------------------------------------------------------------------

        // manage quar index function-------------------------------------------------------
        public function index_quar()
        {
            $ms_tambang = DB::table('ms_tambang')
                        ->where('ms_tambang.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Quarry';
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
            return view('operational/quarry/quarhome', ['ms_tambang' => $ms_tambang]);
        }
        // ---------------------------------------------------------------------------------

        // manage quar add function---------------------------------------------------------
        public function add_quar()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Quarry';
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

            return view('operational/quarry/quaradd');
        }
        // ---------------------------------------------------------------------------------

        // manage quar do add function------------------------------------------------------
        public function do_add_quar(Request $request)
        {
            $this->validate($request, [
                'quarry_name'       => 'required|max:100',
                'quarry_address'    => 'required',
                'no_telp'           => 'max:15',
                'npwp'              => 'max:50'
            ]);

            // validate increment id
                $id = DB::table('ms_tambang')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->tambang_id,4,6);
                    $next_id    = $lastnumber + 1;
                    $id         = "QUAR".sprintf("%06d", $next_id);
                }else{
                    $id         = "QUAR000001";
                }
            // validate increment id

            $data = new manage_quarry;
            $data->tambang_id                   = $id;
            $data->nama                         = $request->quarry_name;
            $data->alamat                       = $request->quarry_address;
            $data->alamat_perusahaan_tambang    = $request->quarry_office;
            $data->no_telp_perusahaan_tambang   = $request->no_telp;
            $data->npwp                         = $request->npwp;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Quarry'; 
                $table_logs  = 'ms_tambang';
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

            return redirect(url('operational/quar_home'))->with('status', ' Add new quarry has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage quar edit function--------------------------------------------------------
        public function edit_quar($id)
        {
           $ms_tambang = DB::table('ms_tambang')
                        ->where('ms_tambang.status', 'A')
                        ->where('ms_tambang.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Quarry';
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

            return view('operational/quarry/quaredit', ['ms_tambang' => $ms_tambang]);
        }
        // ---------------------------------------------------------------------------------

        // manage quar do edit function-----------------------------------------------------
        public function do_edit_quar(Request $request, $id)
        {
            $this->validate($request, [
                'quarry_name'       => 'required|max:100',
                'quarry_address'    => 'required',
                'no_telp'           => 'max:15',
                'npwp'              => 'max:50'
            ]);

            $data = manage_quarry::find($id);
            $data->nama                         = $request->quarry_name;
            $data->alamat                       = $request->quarry_address;
            $data->alamat_perusahaan_tambang    = $request->quarry_office;
            $data->no_telp_perusahaan_tambang   = $request->no_telp;
            $data->npwp                         = $request->npwp;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Quarry'; 
                $table_logs  = 'ms_tambang';
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

            return redirect(url('operational/quar_home'))->with('status', ' Updated quarry has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage quar delete function------------------------------------------------------
        public function delete_quar($id)
        {
            $data = manage_quarry::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Quarry'; 
                $table_logs  = 'ms_tambang ';
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

            return redirect(url('operational/quar_home'))->with('status', ' Deleted quarry has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage quar range function-------------------------------------------------------
        public function quar_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idquar;
            $name       = $request->name;
            $phone      = $request->phone;
            $address    = $request->address;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $phone == "" &&  $address == ""){    
               
                return redirect(url('operational/quar_home'));

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
                $id_sc          = $id != "" ? "AND a.tambang_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $phone_sc       = $phone != "" ? "AND a.no_telp_perusahaan_tambang LIKE '%".$phone."%'" : "";
                $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";

                // query range
                $ms_tambang = DB::select("SELECT a.*
                                FROM ms_tambang a 
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $phone_sc $address_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$phone,$id,$address];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Quarry'; 
                    $table_logs  = 'ms_tambang';
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
                return view('operational/quarry/quarhome')->with('ms_tambang',$ms_tambang)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage quar export function------------------------------------------------------
        public function quar_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idquar;
            $name       = $request->name;
            $phone      = $request->phone;
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
            $id_sc          = $id != "" ? "AND a.tambang_id LIKE '%".$id."%'" : "";
            $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
            $phone_sc       = $phone != "" ? "AND a.no_telp_perusahaan_tambang LIKE '%".$phone."%'" : "";
            $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_tambang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.tambang_id AS 'quarid', a.nama AS 'name', a.alamat AS 'address',
                            a.alamat_perusahaan_tambang AS 'address2',
                            a.no_telp_perusahaan_tambang AS 'contact', a.npwp AS 'npwp'
                            FROM ms_tambang a 
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $name_sc $phone_sc $address_sc
                            ORDER BY a.created_date DESC");

            }else{

                $ms_tambang = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.tambang_id AS 'quarid', a.nama AS 'name', a.alamat AS 'address',
                            a.alamat_perusahaan_tambang AS 'address2',
                            a.no_telp_perusahaan_tambang AS 'contact', a.npwp AS 'npwp'
                            FROM ms_tambang a 
                            WHERE a.`status` = 'A'
                            $id_sc $name_sc $phone_sc $address_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Quarry - '.date("d-m-Y").'', function($result) use ($ms_tambang, $date_start_format, $date_end_format) {

                $result->sheet('Data Quarry', function($sheet) use($ms_tambang,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_tambang as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->quarid,
                                $item->name,
                                $item->address,
                                $item->address2,
                                $item->contact,
                                $item->npwp
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','QUARRY REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Quarry ID','Name','Address','Office Address', 'Office Contact', 'NPWP'));
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

            $arraydate = [$start,$end,$name,$phone,$id,$address];

            // return to view
            return view('operational/quarry/quarhome', ['ms_tambang' => $ms_tambang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage quar---------------------------------------------------------------------

    // menu manage port---------------------------------------------------------------------

        // manage port index function-------------------------------------------------------
        public function index_port()
        {
            $ms_pelabuhan = DB::table('ms_pelabuhan')
                        ->where('ms_pelabuhan.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Port';
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
            return view('operational/port/porthome', ['ms_pelabuhan' => $ms_pelabuhan]);
        }
        // ---------------------------------------------------------------------------------

        // manage port add function---------------------------------------------------------
        public function add_port()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Port';
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

            return view('operational/port/portadd');
        }
        // ---------------------------------------------------------------------------------

        // manage port do add function------------------------------------------------------
        public function do_add_port(Request $request)
        {
            $this->validate($request, [
                'port_name'         => 'required|max:100',
                'port_address'      => 'required',
                'no_telp'           => 'max:15',
                'npwp'              => 'max:50'
            ]);

            // validate increment id
                $id = DB::table('ms_pelabuhan')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->pelabuhan_id,4,6);
                    $next_id    = $lastnumber + 1;
                    $id         = "PORT".sprintf("%06d", $next_id);
                }else{
                    $id         = "PORT000001";
                }
            // validate increment id

            $data = new manage_port;
            $data->pelabuhan_id                 = $id;
            $data->nama                         = $request->port_name;
            $data->alamat                       = $request->port_address;
            $data->alamat_perusahaan_pelabuhan  = $request->port_office;
            $data->no_telp_perusahaan_pelabuhan = $request->no_telp;
            $data->npwp                         = $request->npwp;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Port'; 
                $table_logs  = 'ms_pelabuhan';
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

            return redirect(url('operational/port_home'))->with('status', ' Add new port has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage port edit function--------------------------------------------------------
        public function edit_port($id)
        {
           $ms_pelabuhan = DB::table('ms_pelabuhan')
                        ->where('ms_pelabuhan.status', 'A')
                        ->where('ms_pelabuhan.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Port';
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

            return view('operational/port/portedit', ['ms_pelabuhan' => $ms_pelabuhan]);
        }
        // ---------------------------------------------------------------------------------

        // manage port do edit function-----------------------------------------------------
        public function do_edit_port(Request $request, $id)
        {
            $this->validate($request, [
                'port_name'         => 'required|max:100',
                'port_address'      => 'required',
                'no_telp'           => 'max:15',
                'npwp'              => 'max:50'
            ]);

            $data = manage_port::find($id);
            $data->nama                         = $request->port_name;
            $data->alamat                       = $request->port_address;
            $data->alamat_perusahaan_pelabuhan  = $request->port_office;
            $data->no_telp_perusahaan_pelabuhan = $request->no_telp;
            $data->npwp                         = $request->npwp;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Port'; 
                $table_logs  = 'ms_pelabuhan';
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

            return redirect(url('operational/port_home'))->with('status', ' Updated port has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage port delete function------------------------------------------------------
        public function delete_port($id)
        {
            $data = manage_port::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Port'; 
                $table_logs  = 'ms_pelabuhan ';
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

            return redirect(url('operational/port_home'))->with('status', ' Deleted port has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage port range function-------------------------------------------------------
        public function port_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idport;
            $name       = $request->name;
            $phone      = $request->phone;
            $address    = $request->address;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $name == "" &&  $phone == "" &&  $address == ""){    
               
                return redirect(url('operational/port_home'));

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
                $id_sc          = $id != "" ? "AND a.pelabuhan_id LIKE '%".$id."%'" : "";
                $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
                $phone_sc       = $phone != "" ? "AND a.no_telp_perusahaan_pelabuhan LIKE '%".$phone."%'" : "";
                $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";

                // query range
                $ms_pelabuhan = DB::select("SELECT a.*
                                FROM ms_pelabuhan a 
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $name_sc $phone_sc $address_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$phone,$id,$address];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Port'; 
                    $table_logs  = 'ms_pelabuhan';
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
                return view('operational/port/porthome')->with('ms_pelabuhan',$ms_pelabuhan)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage port export function------------------------------------------------------
        public function port_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->idquar;
            $name       = $request->name;
            $phone      = $request->phone;
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
            $id_sc          = $id != "" ? "AND a.port_id LIKE '%".$id."%'" : "";
            $name_sc        = $name != "" ? "AND a.nama LIKE '%".$name."%'" : "";
            $phone_sc       = $phone != "" ? "AND a.no_telp_perusahaan_tambang LIKE '%".$phone."%'" : "";
            $address_sc     = $address != "" ? "AND a.alamat LIKE '%".$address."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $ms_pelabuhan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.pelabuhan_id AS 'portid', a.nama AS 'name', a.alamat AS 'address',
                            a.alamat_perusahaan_pelabuhan AS 'address2',
                            a.no_telp_perusahaan_pelabuhan AS 'contact', a.npwp AS 'npwp'
                            FROM ms_pelabuhan a 
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $name_sc $phone_sc $address_sc
                            ORDER BY a.created_date DESC");

            }else{

                $ms_pelabuhan = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.pelabuhan_id AS 'portid', a.nama AS 'name', a.alamat AS 'address',
                            a.alamat_perusahaan_pelabuhan AS 'address2',
                            a.no_telp_perusahaan_pelabuhan AS 'contact', a.npwp AS 'npwp'
                            FROM ms_pelabuhan a 
                            WHERE a.`status` = 'A'
                            $id_sc $name_sc $phone_sc $address_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Port - '.date("d-m-Y").'', function($result) use ($ms_pelabuhan, $date_start_format, $date_end_format) {

                $result->sheet('Data Port', function($sheet) use($ms_pelabuhan,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($ms_pelabuhan as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->portid,
                                $item->name,
                                $item->address,
                                $item->address2,
                                $item->contact,
                                $item->npwp
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','PORT REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Port ID','Name','Address','Office Address', 'Office Contact', 'NPWP'));
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

            $arraydate = [$start,$end,$name,$phone,$id,$address];

            // return to view
            return view('operational/port/porthome', ['ms_pelabuhan' => $ms_pelabuhan, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage port---------------------------------------------------------------------

    // menu manage solar--------------------------------------------------------------------

        // manage solar index function------------------------------------------------------
        public function index_solar_type()
        {
            // query left join
            $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Solar';
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
            return view('operational/solar_type/solartypehome', ['lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage solar add function--------------------------------------------------------
        public function add_solar_type()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Solar';
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

            return view('operational/solar_type/solartypeadd');
        }
        // ---------------------------------------------------------------------------------

        // manage solar do add function-----------------------------------------------------
        public function do_add_solar_type(Request $request)
        {
            $this->validate($request, [
                'name'              => 'required|min:3|max:100',
                'price'             => 'required|max:15'
            ]);

            $data = new manage_solar_type;
            $data->name                         = $request->name;
            $data->harga_liter                  = $request->price;
            $data->created_date                 = date('Y-m-d H:i:s');
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Solar'; 
                $table_logs  = 'lt_solar';
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

            return redirect(url('operational/solar_type_home'))->with('status', ' Add new solar type has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar edit function-------------------------------------------------------
        public function edit_solar_type($id)
        {
           $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Solar';
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

            return view('operational/solar_type/solartypeedit', ['lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage solar do edit function----------------------------------------------------
        public function do_edit_solar_type(Request $request, $id)
        {
            $this->validate($request, [
                'name'              => 'required|min:3|max:100',
                'price'             => 'required|max:15'
            ]);

            $data = manage_solar_type::find($id);
            $data->name                         = $request->name;
            $data->harga_liter                  = $request->price;
            $data->last_modify_date             = date('Y-m-d H:i:s');
            $data->modify_user_id               = Auth::user()->karyawan_id;
            $data->status                       = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Solar'; 
                $table_logs  = 'lt_solar';
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

            return redirect(url('operational/solar_type_home'))->with('status', ' Updated solar type has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar delete function-----------------------------------------------------
        public function delete_solar_type($id)
        {
            $data = manage_solar_type::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Solar'; 
                $table_logs  = 'lt_solar ';
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

            return redirect(url('operational/solar_type_home'))->with('status', ' Deleted solar type has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar range function------------------------------------------------------
        public function solar_type_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $price      = $request->price;
            $name       = $request->name;

            // validate empty
            if($start == "" && $end == "" && $price == "" &&  $name == ""){    
               
                return redirect(url('operational/solar_type_home'));

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
                $price_sc       = $price != "" ? "AND a.harga_liter LIKE '%".$price."%'" : "";
                $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";

                // query range
                $lt_solar = DB::select("SELECT a.*
                                FROM lt_solar a 
                                WHERE a.`status` = 'A'
                                $date_sc $name_sc $price_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$name,$price];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Solary Type'; 
                    $table_logs  = 'lt_solar';
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
                return view('operational/solar_type/solartypehome')->with('lt_solar',$lt_solar)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage port export function------------------------------------------------------
        public function solar_type_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $price      = $request->price;
            $name       = $request->name;

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
            $price_sc       = $price != "" ? "AND a.harga_liter LIKE '%".$price."%'" : "";
            $name_sc        = $name != "" ? "AND a.name LIKE '%".$name."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $lt_solar = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.name AS 'name',
                                a.harga_liter AS 'price'
                                FROM lt_solar a 
                                WHERE a.`status` = 'A'
                                $date_sc $name_sc $price_sc
                                ORDER BY a.created_date DESC");

            }else{

                $lt_solar = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.name AS 'name',
                                a.harga_liter AS 'price'
                                FROM lt_solar a 
                                WHERE a.`status` = 'A'
                                $name_sc $price_sc
                                ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Solar Type - '.date("d-m-Y").'', function($result) use ($lt_solar, $date_start_format, $date_end_format) {

                $result->sheet('Data Solar Type', function($sheet) use($lt_solar,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $count = 0;
                    foreach($lt_solar as $item){

                          $i++;
                          $count++;
                          $price = number_format($item->price);
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->name,
                                $price
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SOLAR TYPE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','Name','Price liter'));
                    $sheet->setBorder('A9:C9', 'thin');

                    // set style column


                    $sheet->cells('A9:C9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:C1');
                    $sheet->cells('A1:C1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':C'.$k, 'thin');
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

            $arraydate = [$start,$end,$name,$price];

            // return to view
            return view('operational/solar_type/solartypehome', ['lt_solar' => $lt_solar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage solar--------------------------------------------------------------------

    // menu solar usage---------------------------------------------------------------------

        // manage solar usage index function------------------------------------------------
        public function index_solar()
        {
            // query left join
            $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

            // query left join
           $lt_pemakaiansolar = DB::table('lt_pemakaiansolar')
                        ->select('lt_pemakaiansolar.*','lt_solar.name AS name_solar', 'lt_solar.harga_liter AS price')
                        ->leftJoin('lt_solar', 'lt_pemakaiansolar.solar_type_id', '=', 'lt_solar.id')
                        ->where('lt_pemakaiansolar.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Solar Usage';
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
            return view('operational/solarusage/solarhome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage select2 car function------------------------------------------
        public function search_car()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_mobil = DB::table('ms_mobil')
                            ->select('ms_mobil.*', 'ms_mobil.customer_id AS cust_id', 'ms_mobil.mobil_id AS car_id')
                            ->leftJoin('ms_karyawan', 'ms_mobil.customer_id', '=', 'ms_karyawan.karyawan_id')
                            ->leftJoin('ms_customer', 'ms_mobil.customer_id', '=', 'ms_customer.customer_id')
                            ->where('ms_mobil.status',"A")
                            ->where('ms_mobil.mobil_id','like', "%".$term."%" )
                            ->orWhere('ms_mobil.customer_id','like', "%".$term."%" )
                            ->orWhere('ms_karyawan.nama','like', "%".$term."%" )
                            ->orWhere('ms_customer.nama','like', "%".$term."%" )
                            ->get();

            $query = $ms_mobil;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->car_id));
                    $new_row['text']=htmlentities(stripslashes($row->car_id ." - ". $row->cust_id));
                    $new_row['name']=htmlentities(stripslashes($row->cust_id));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage select2 employee function-------------------------------------
        public function search_employee_solar()
        {
            $term = strip_tags(trim($_GET['q']));

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
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage add function--------------------------------------------------
        public function add_solar()
        {
            // query left join
            $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Solar Usage';
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

            return view('operational/solarusage/solaradd',['lt_solar'=>$lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage do add function-----------------------------------------------
        public function do_add_solar(Request $request)
        {
            $this->validate($request, [
                'car_id'             => 'required|min:1|max:20',
                'emp_id'             => 'required|min:1|max:20',
                'charger'            => 'required|max:100',
                'solar_date'         => 'required',
                'solar_type'         => 'required',
                'liter'              => 'required|max:10'
            ]);

            // validate increment id
                $id = DB::table('lt_pemakaiansolar')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->pemakaian_solar_id,3,7);
                    $next_id    = $lastnumber + 1;
                    $id         = "SOL".sprintf("%07d", $next_id);
                }else{
                    $id         = "SOL0000001";
                }
            // validate increment id

            $data = new manage_solar;
            $data->pemakaian_solar_id                    = $id;
            $data->mobil_id                              = $request->car_id;
            $data->karyawan_id                           = $request->emp_id;
            $data->nama_pengisi                          = $request->charger;
            $data->tanggal_pemakaian                     = $request->solar_date;
            $data->no_nota                               = $request->no_nota;
            $data->solar_type_id                         = $request->solar_type;
            $data->liter_pemakaian_solar                 = $request->liter;
            $data->created_date                          = date('Y-m-d H:i:s');
            $data->last_modify_date                      = date('Y-m-d H:i:s');
            $data->modify_user_id                        = Auth::user()->karyawan_id;
            $data->status                                = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Solar Usage'; 
                $table_logs  = 'lt_pemakaiansolar';
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

            return redirect(url('operational/solar_home'))->with('status', ' Add new solar has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage edit function-------------------------------------------------
        public function edit_solar($id)
        {
            // query left join
            $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

            $lt_pemakaiansolar = DB::table('lt_pemakaiansolar')
                                ->select('lt_pemakaiansolar.*','lt_solar.id AS id_solar', 'lt_solar.name AS name_solar', 'lt_solar.harga_liter AS price')
                                ->leftJoin('lt_solar', 'lt_pemakaiansolar.solar_type_id', '=', 'lt_solar.id')
                                ->where('lt_pemakaiansolar.status', 'A')
                                ->where('lt_pemakaiansolar.id', $id)
                                ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Solar Usage';
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

            return view('operational/solarusage/solaredit', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage do edit function----------------------------------------------
        public function do_edit_solar(Request $request, $id)
        {
            $this->validate($request, [
                'charger'            => 'required|max:100',
                'solar_date'         => 'required',
                'solar_type'         => 'required',
                'liter'              => 'required|max:10'
            ]);

            $data = manage_solar::find($id);
            $data->nama_pengisi                          = $request->charger;
            $data->tanggal_pemakaian                     = $request->solar_date;
            $data->no_nota                               = $request->no_nota;
            $data->solar_type_id                         = $request->solar_type;
            $data->liter_pemakaian_solar                 = $request->liter;
            $data->last_modify_date                      = date('Y-m-d H:i:s');
            $data->modify_user_id                        = Auth::user()->karyawan_id;
            $data->status                                = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Solar Usage'; 
                $table_logs  = 'lt_pemakaiansolar';
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


            return redirect(url('operational/solar_home'))->with('status', ' Updated solar has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage delete function-----------------------------------------------
        public function delete_solar($id)
        {
            $data = manage_solar::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Solar Usage'; 
                $table_logs  = 'lt_pemakaiansolar';
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

            return redirect(url('operational/solar_home'))->with('status', ' Deleted solar has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage range function------------------------------------------------
        public function solar_range(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $id             = $request->solarid;
            $carid          = $request->carid;
            $type           = $request->type;
            $name           = $request->name;
            $price          = $request->price;

            // validate empty
            if($start == "" && $end == "" && $id == "" &&  $carid == "" &&  $type == "" &&  $name == "" && $price == ""){    
                
                return redirect(url('operational/solar_home'));

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
                $id_sc          = $id != "" ? "AND a.pemakaian_solar_id LIKE '%".$id."%'" : "";
                $carid_sc       = $carid != "" ? "AND a.mobil_id LIKE '%".$carid."%'" : "";
                $type_sc        = $type != "" ? "AND b.id = '".$type."'" : "";
                $name_sc        = $name != "" ? "AND a.nama_pengisi LIKE '%".$name."%'" : "";
                $price_sc       = $price != "" ? "AND b.harga_liter = '".$price."'" : "";

                // query range
                $lt_pemakaiansolar = DB::select("SELECT a.*, b.name AS 'name_solar', b.harga_liter AS 'price'
                                FROM lt_pemakaiansolar a LEFT JOIN lt_solar b
                                ON a.solar_type_id = b.id
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $carid_sc $type_sc $name_sc $price_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$carid,$type,$id,$name,$price];

                // load solar type
                $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Solar Usage'; 
                    $table_logs  = 'lt_pemakaiansolar';
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
                return view('operational/solarusage/solarhome')->with('lt_pemakaiansolar',$lt_pemakaiansolar)->with('arraydate',$arraydate)->with('lt_solar',$lt_solar);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage export function-----------------------------------------------
        public function solar_export(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $id             = $request->solarid;
            $carid          = $request->carid;
            $type           = $request->type;
            $name           = $request->name;
            $price          = $request->price;

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
            $id_sc          = $id != "" ? "AND a.pemakaian_solar_id LIKE '%".$id."%'" : "";
            $carid_sc       = $carid != "" ? "AND a.mobil_id LIKE '%".$carid."%'" : "";
            $type_sc        = $type != "" ? "AND b.id = '".$type."'" : "";
            $name_sc        = $name != "" ? "AND a.nama_pengisi LIKE '%".$name."%'" : "";
            $price_sc       = $price != "" ? "AND b.harga_liter = '".$price."'" : "";

            // validate export date
            if($request->dateStart != ""){

                $lt_pemakaiansolar = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                                a.pemakaian_solar_id AS 'solarid',
                                a.mobil_id AS 'carid', a.karyawan_id AS 'empid', a.nama_pengisi AS 'chargername',
                                DATE_FORMAT(a.tanggal_pemakaian,'%d/%m/%Y') AS 'dateusage', a.no_nota AS 'nota',
                                c.nama AS 'employeename', d.customer_id AS 'ownerid',
                                (CASE WHEN d.type_car <> 1 THEN e.nama ELSE f.nama  END) AS 'ownername',

                                b.name AS 'name_solar', b.harga_liter AS 'price', a.liter_pemakaian_solar AS 'literusage'
                                FROM lt_pemakaiansolar a LEFT JOIN lt_solar b
                                ON a.solar_type_id = b.id
                                LEFT JOIN ms_karyawan c
                                ON a.karyawan_id = c.karyawan_id
                                LEFT JOIN ms_mobil d
                                ON a.mobil_id = d.mobil_id
                                LEFT JOIN ms_customer e
                                ON d.customer_id = e.customer_id
                                LEFT JOIN ms_karyawan f
                                ON d.customer_id = f.karyawan_id

                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $carid_sc $type_sc $name_sc $price_sc
                                ORDER BY a.created_date DESC");

            }else{

                $lt_pemakaiansolar = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                                a.pemakaian_solar_id AS 'solarid',
                                a.mobil_id AS 'carid', a.karyawan_id AS 'empid', a.nama_pengisi AS 'chargername',
                                DATE_FORMAT(a.tanggal_pemakaian,'%d/%m/%Y') AS 'dateusage', a.no_nota AS 'nota',
                                c.nama AS 'employeename', d.customer_id AS 'ownerid',
                                (CASE WHEN d.type_car <> 1 THEN e.nama ELSE f.nama  END) AS 'ownername',

                                b.name AS 'name_solar', b.harga_liter AS 'price', a.liter_pemakaian_solar AS 'literusage'
                                FROM lt_pemakaiansolar a LEFT JOIN lt_solar b
                                ON a.solar_type_id = b.id
                                LEFT JOIN ms_karyawan c
                                ON a.karyawan_id = c.karyawan_id
                                LEFT JOIN ms_mobil d
                                ON a.mobil_id = d.mobil_id
                                LEFT JOIN ms_customer e
                                ON d.customer_id = e.customer_id
                                LEFT JOIN ms_karyawan f
                                ON d.customer_id = f.karyawan_id

                                WHERE a.`status` = 'A'
                                $id_sc $carid_sc $type_sc $name_sc $price_sc
                                ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Solar Usage - '.date("d-m-Y").'', function($result) use ($lt_pemakaiansolar, $date_start_format, $date_end_format) {

                $result->sheet('Data Solar Usage', function($sheet) use($lt_pemakaiansolar,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $total = 0;
                    $count = 0;
                    foreach($lt_pemakaiansolar as $item){

                          $i++;
                          $count++;
                          $total = $item->price * $item->literusage;
                          $price = number_format($item->price);
                          $total = number_format($total);
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->solarid,
                                $item->carid,
                                $item->ownerid,
                                $item->ownername,
                                $item->employeename,
                                $item->chargername,
                                $item->dateusage,
                                $item->nota,
                                $item->name_solar,
                                $price,
                                $item->literusage,
                                $total
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','SOLAR USAGE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','ID','Car ID','Owner ID','Owner Name', 'Employee Name', 'Charger Name', 'Date Usage', 'Nota', 'Solar Name', 'Price Liter', 'Liter Usage','Total Price'));
                    $sheet->setBorder('A9:M9', 'thin');

                    // set style column


                    $sheet->cells('A9:M9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:M1');
                    $sheet->cells('A1:M1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':M'.$k, 'thin');
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

            $arraydate = [$start,$end,$id,$name,$category,$spec,$statusitems];

            // return to view
            return view('operational/solarusage/solarhome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu solar usage---------------------------------------------------------------------

    // menu manage route--------------------------------------------------------------------

        // manage manage route index function-----------------------------------------------
        public function index_route()
        {
            $lt_route = DB::table('lt_route')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Route';
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
            return view('operational/route/routehome', ['lt_route' => $lt_route]);
        }
        // ---------------------------------------------------------------------------------

        // manage manage route select2 port/quar/cust function------------------------------
        public function search_route_a()
        {
            $term = strip_tags(trim($_GET['q']));
            $typeterm = strip_tags(trim($_GET['j']));

            if($typeterm == 1){
                $ms_tambang = DB::table('ms_tambang')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('tambang_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_tambang;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->tambang_id));
                        $new_row['text']=htmlentities(stripslashes($row->tambang_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }

            }else if($typeterm == 2){
                $ms_pelabuhan = DB::table('ms_pelabuhan')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('pelabuhan_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_pelabuhan;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->pelabuhan_id));
                        $new_row['text']=htmlentities(stripslashes($row->pelabuhan_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }
            }else if($typeterm == 3){
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

        // manage manage route select2 port/quar/cust function------------------------------
        public function search_route_b()
        {
            $term = strip_tags(trim($_GET['q']));
            $typeterm = strip_tags(trim($_GET['j']));

            if($typeterm == 1){
                $ms_tambang = DB::table('ms_tambang')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('tambang_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_tambang;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->tambang_id));
                        $new_row['text']=htmlentities(stripslashes($row->tambang_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }

            }else if($typeterm == 2){
                $ms_pelabuhan = DB::table('ms_pelabuhan')
                                ->where('status',"A")
                                ->where('nama','like', "%".$term."%" )
                                ->orWhere('pelabuhan_id','like', "%".$term."%" )
                                ->get();

                $query = $ms_pelabuhan;

                if(sizeof($query) > 0){
                    foreach ($query as $row){
                        $new_row['id']=htmlentities(stripslashes($row->pelabuhan_id));
                        $new_row['text']=htmlentities(stripslashes($row->pelabuhan_id." - ".$row->nama));
                        $new_row['name']=htmlentities(stripslashes($row->nama));
                        $row_set[] = $new_row; //build an array
                    }
                    return json_encode($row_set); //format the array into json data
                }
            }else if($typeterm == 3){
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

        // manage manage route add function-------------------------------------------------
        public function add_route()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Route';
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

            return view('operational/route/routeadd');
        }
        // ---------------------------------------------------------------------------------

        // manage manage route do add function----------------------------------------------
        public function do_add_route(Request $request)
        {
            $this->validate($request, [
                'route_type_a'      => 'required|min:1|max:10',
                'route_type_b'      => 'required|min:1|max:10',
                'route_a'           => 'required|max:100',
                'route_b'           => 'required|max:100',
                'distance'          => 'required|max:10',
                'hour'              => 'max:10',
                'minute'            => 'max:10',
                'second'            => 'max:10'
            ]);

            // validate increment id
                $id = DB::table('lt_route')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->route_id,2,8);
                    $next_id    = $lastnumber + 1;
                    $id         = "RT".sprintf("%08d", $next_id);
                }else{
                    $id         = "RT00000001";
                }
            // validate increment id

            $data = new manage_route;
            $data->route_id                              = $id;
            $data->route_type_a                          = $request->route_type_a;
            $data->route_type_b                          = $request->route_type_b;
            $data->route_a                               = $request->route_a;
            $data->route_b                               = $request->route_b;
            $data->distance                              = $request->distance;

            if($request->hour != ""){
                $data->hour                              = $request->hour;
            }else{
                $data->hour                              = 0;
            }

            if($request->minute != ""){
                $data->minute                            = $request->minute;
            }else{
                $data->minute                            = 0;
            }

            if($request->second != ""){
                $data->second                            = $request->second;
            }else{
                $data->second                            = 0;
            }

            $data->liter                                 = $request->liter;
            if(Auth::user()->level_id == 0 || Auth::user()->level_id == 1){
                $data->komisi                            = $request->komisi;
            }

            $data->created_date                          = date('Y-m-d H:i:s');
            $data->last_modify_date                      = date('Y-m-d H:i:s');
            $data->modify_user_id                        = Auth::user()->karyawan_id;
            $data->status                                = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Route'; 
                $table_logs  = 'lt_route';
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

            return redirect(url('operational/route_home'))->with('status', ' Add new route has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage manage route edit function------------------------------------------------
        public function edit_route($id)
        {
            $lt_route = DB::table('lt_route')
                                ->where('status', 'A')
                                ->where('id', $id)
                                ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Route';
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

            return view('operational/route/routeedit', ['lt_route' => $lt_route]);
        }
        // ---------------------------------------------------------------------------------

        // manage manage route do_edit function---------------------------------------------
        public function do_edit_route(Request $request, $id)
        {
            $this->validate($request, [
                'route_type_a'      => 'required|min:1|max:10',
                'route_type_b'      => 'required|min:1|max:10',
                'route_a'           => 'required|max:100',
                'route_b'           => 'required|max:100',
                'distance'          => 'required|max:10',
                'hour'              => 'max:10',
                'minute'            => 'max:10',
                'second'            => 'max:10'
            ]);

            $data = manage_route::find($id);
            $data->route_type_a                          = $request->route_type_a;
            $data->route_type_b                          = $request->route_type_b;
            $data->route_a                               = $request->route_a;
            $data->route_b                               = $request->route_b;
            $data->distance                              = $request->distance;
            if($request->hour != ""){
                $data->hour                              = $request->hour;
            }else{
                $data->hour                              = 0;
            }

            if($request->minute != ""){
                $data->minute                            = $request->minute;
            }else{
                $data->minute                            = 0;
            }

            if($request->second != ""){
                $data->second                            = $request->second;
            }else{
                $data->second                            = 0;
            }
            $data->liter                                 = $request->liter;
            if(Auth::user()->level_id == 0 || Auth::user()->level_id == 1){
                $data->komisi                            = $request->komisi;
            }
            $data->last_modify_date                      = date('Y-m-d H:i:s');
            $data->modify_user_id                        = Auth::user()->karyawan_id;
            $data->status                                = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Route'; 
                $table_logs  = 'lt_route';
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

            return redirect(url('operational/route_home'))->with('status', ' Updated route has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage manage route delete function----------------------------------------------
        public function delete_route($id)
        {
            $data = manage_route::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

             // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Route'; 
                $table_logs  = 'lt_route ';
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

            return redirect(url('operational/route_home'))->with('status', ' Deleted route has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage manage route range function-----------------------------------------------
        public function route_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $routea     = $request->routea;
            $routeb     = $request->routeb;
            $id         = $request->routeid;
            $distance   = $request->distance;

            // validate empty
            if($start == "" && $end == "" && $routea == "" &&  $routeb == "" &&  $id == "" &&  $distance == ""){    
               
                return redirect(url('operational/route_home'));

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
                $id_sc          = $id != "" ? "AND a.route_id LIKE '%".$id."%'" : "";
                $routea_sc      = $routea != "" ? "AND a.route_type_a LIKE '%".$routea."%'" : "";
                $routeb_sc      = $routeb != "" ? "AND a.route_type_b LIKE '%".$routeb."%'" : "";
                $distance_sc    = $distance != "" ? "AND a.distance LIKE '%".$distance."%'" : "";

                // query range
                $lt_route = DB::select("SELECT a.*
                                FROM lt_route a 
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $routea_sc $routeb_sc $distance_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$routea,$routeb,$id,$distance];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Route'; 
                    $table_logs  = 'lt_route';
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
                return view('operational/route/routehome')->with('lt_route',$lt_route)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage export function-----------------------------------------------
        public function route_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $routea     = $request->routea;
            $routeb     = $request->routeb;
            $id         = $request->routeid;
            $distance   = $request->distance;

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
            $id_sc          = $id != "" ? "AND a.route_id LIKE '%".$id."%'" : "";
            $routea_sc      = $routea != "" ? "AND a.route_type_a LIKE '%".$routea."%'" : "";
            $routeb_sc      = $routeb != "" ? "AND a.route_type_b LIKE '%".$routeb."%'" : "";
            $distance_sc    = $distance != "" ? "AND a.distance LIKE '%".$distance."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $lt_route = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.route_id AS 'routeid', 
                                a.route_type_a AS 'routetypea', a.route_type_b AS 'routetypeb',
                                a.route_a AS 'routea', a.route_b AS 'routeb',
                                a.distance AS 'distance', a.hour AS 'hours', a.minute AS 'minutes',
                                a.second AS 'seconds', a.liter AS 'liter', a.komisi AS 'komisi'
                                FROM lt_route a
                                WHERE a.status = 'A'
                                $date_sc $id_sc $routea_sc $routeb_sc $distance_sc
                                ORDER BY a.created_date DESC");

            }else{

                $lt_route = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate', a.route_id AS 'routeid', 
                                a.route_type_a AS 'routetypea', a.route_type_b AS 'routetypeb',
                                a.route_a AS 'routea', a.route_b AS 'routeb',
                                a.distance AS 'distance', a.hour AS 'hours', a.minute AS 'minutes',
                                a.second AS 'seconds', a.liter AS 'liter', a.komisi AS 'komisi'
                                FROM lt_route a
                                WHERE a.status = 'A'
                                $id_sc $routea_sc $routeb_sc $distance_sc
                                ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Route - '.date("d-m-Y").'', function($result) use ($lt_route, $date_start_format, $date_end_format) {

                $result->sheet('Data Route', function($sheet) use($lt_route,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $total = 0;
                    $count = 0;
                    foreach($lt_route as $item){

                          $i++;
                          $count++;
                          $total = number_format($item->komisi);
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->routeid,
                                $item->routetypea,
                                $item->routea,
                                $item->routetypeb,
                                $item->routeb,
                                $item->hours,
                                $item->minutes,
                                $item->seconds,
                                $item->liter,
                                $total
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','ROUTE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','ID','Route Type A','Route A','Route Type B', 'Route B', 'Hours', 'Minutes', 'Seconds', 'Liter', 'Commission'));
                    $sheet->setBorder('A9:K9', 'thin');

                    // set style column


                    $sheet->cells('A9:K9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:K1');
                    $sheet->cells('A1:K1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':K'.$k, 'thin');
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

            $arraydate = [$start,$end,$routea,$routeb,$id,$distance];

            // return to view
            return view('operational/route/routehome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage route--------------------------------------------------------------------

    // menu manage tonase-------------------------------------------------------------------

        // manage tonase index function-----------------------------------------------------
        public function index_tonase()
        {
            // query left join
            $tr_tonase = DB::table('tr_tonase')
                        ->where('status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Manage Tonase';
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
            return view('operational/tonase/tonasehome', ['tr_tonase' => $tr_tonase]);
        }
        // ---------------------------------------------------------------------------------

        // manage tonase select2 route function---------------------------------------------
        public function search_tonase_route()
        {
            $term = strip_tags(trim($_GET['q']));

            $lt_route = DB::table('lt_route')
                            ->where('status',"A")
                            ->where('route_id','like', "%".$term."%" )
                            ->orWhere('route_a','like', "%".$term."%" )
                            ->orWhere('route_b','like', "%".$term."%" )
                            ->get();

            $query = $lt_route;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->route_id));
                    $new_row['text']=htmlentities(stripslashes($row->route_id .' : '.$row->route_a." - ".$row->route_b));
                    $new_row['route_a']=htmlentities(stripslashes($row->route_a));
                    $new_row['route_b']=htmlentities(stripslashes($row->route_b));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage tonase add function-------------------------------------------------------
        public function add_tonase()
        {
            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Manage Tonase';
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


            return view('operational/tonase/tonaseadd');
        }
        // ---------------------------------------------------------------------------------

        // manage tonase do add function----------------------------------------------------
        public function do_add_tonase(Request $request)
        {
            $this->validate($request, [
                'route_id'        => 'required|max:20',
                'tonase_p_a'      => 'required|max:20',
                'tonase_n_a'      => 'required|max:20',
                'tonase_p_b'      => 'required|max:20',
                'tonase_n_b'      => 'required|max:20'
            ]);

            // validate increment id
                $id = DB::table('tr_tonase')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->id_tonase,3,7);
                    $next_id    = $lastnumber + 1;
                    $id         = "TON".sprintf("%07d", $next_id);
                }else{
                    $id         = "TON0000001";
                }
            // validate increment id

            $data = new manage_tonase;
            $data->id_tonase        = $id;
            $data->id_route         = $request->route_id;
            $data->tonase_percent_a = $request->tonase_p_a;
            $data->tonase_a         = $request->tonase_n_a;
            $data->tonase_percent_b = $request->tonase_p_b;
            $data->tonase_b         = $request->tonase_n_b;
            $data->created_date     = date('Y-m-d H:i:s');
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Tonase'; 
                $table_logs  = 'tr_tonase';
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

            return redirect(url('operational/tonase_home'))->with('status', ' Created new tonase has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage tonase edit function------------------------------------------------------
        public function edit_tonase($id)
        {

            $tr_tonase = DB::table('tr_tonase')
                        ->where('status', 'A')
                        ->where('id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Manage Tonase';
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

            return view('operational/tonase/tonaseedit', ['tr_tonase' => $tr_tonase]);
        }
        // ---------------------------------------------------------------------------------

        // tonase edit do edit function
        public function do_edit_tonase(Request $request, $id)
        {
            $this->validate($request, [
                'route_id'        => 'required|max:20',
                'tonase_p_a'      => 'required|max:20',
                'tonase_n_a'      => 'required|max:20',
                'tonase_p_b'      => 'required|max:20',
                'tonase_n_b'      => 'required|max:20'
            ]);

            $data = manage_tonase::find($id);
            $data->id_route         = $request->route_id;
            $data->tonase_percent_a = $request->tonase_p_a;
            $data->tonase_a         = $request->tonase_n_a;
            $data->tonase_percent_b = $request->tonase_p_b;
            $data->tonase_b         = $request->tonase_n_b;
            $data->last_modify_date = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Tonase'; 
                $table_logs  = 'tr_tonase';
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

            return redirect(url('operational/tonase_home'))->with('status', ' Updated tonase has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage tonase delete function----------------------------------------------------
        public function delete_tonase($id)
        {
            $data = manage_tonase::find($id);
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Tonase'; 
                $table_logs  = 'tr_tonase ';
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

            return redirect(url('operational/tonase_home'))->with('status', ' Deleted tonase has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage tonase range function-----------------------------------------------------
        public function tonase_range(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->tonaseid;
            $percenta   = $request->percenta;
            $percentb   = $request->percentb;
            $numbera    = $request->numbera;
            $numberb    = $request->numberb;

            // validate empty
            if($start == "" && $end == "" && $percenta == "" &&  $percentb == "" &&  $numbera == "" &&  $numberb == "" && $id == ""){    
               
                return redirect(url('operational/tonase_home'));

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
                $id_sc          = $id != "" ? "AND a.id_tonase LIKE '%".$id."%'" : "";
                $percenta_sc    = $percenta != "" ? "AND a.tonase_percent_a LIKE '%".$percenta."%'" : "";
                $percentb_sc    = $percentb != "" ? "AND a.tonase_percent_b LIKE '%".$percentb."%'" : "";
                $numbera_sc     = $numbera != "" ? "AND a.tonase_a LIKE '%".$numbera."%'" : "";
                $numberb_sc     = $numberb != "" ? "AND a.tonase_b LIKE '%".$numberb."%'" : "";

                // query range
                $tr_tonase = DB::select("SELECT a.*
                                FROM tr_tonase a 
                                WHERE a.`status` = 'A'
                                $date_sc $id_sc $percenta_sc $percentb_sc $numbera_sc $numberb_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$percenta,$numbera,$id,$percentb,$numberb];

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Tonase'; 
                    $table_logs  = 'tr_tonase';
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
                return view('operational/tonase/tonasehome')->with('tr_tonase',$tr_tonase)->with('arraydate',$arraydate);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage solar usage export function-----------------------------------------------
        public function tonase_export(Request $request)
        {
            //get date and get all entities
            $start      = $request->dateStart;
            $end        = $request->dateEnd;
            $id         = $request->tonaseid;
            $percenta   = $request->percenta;
            $percentb   = $request->percentb;
            $numbera    = $request->numbera;
            $numberb    = $request->numberb;

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
            $id_sc          = $id != "" ? "AND a.id_tonase LIKE '%".$id."%'" : "";
            $percenta_sc    = $percenta != "" ? "AND a.tonase_percent_a LIKE '%".$percenta."%'" : "";
            $percentb_sc    = $percentb != "" ? "AND a.tonase_percent_b LIKE '%".$percentb."%'" : "";
            $numbera_sc     = $numbera != "" ? "AND a.tonase_a LIKE '%".$numbera."%'" : "";
            $numberb_sc     = $numberb != "" ? "AND a.tonase_b LIKE '%".$numberb."%'" : "";

            // validate export date
            if($request->dateStart != ""){

                $tr_tonase = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.id_tonase AS 'tonaseid', a.id_route AS 'routeid',
                            a.tonase_percent_a AS 'percenta', a.tonase_a AS 'numbera', 
                            a.tonase_percent_b AS 'percentb', a.tonase_b AS 'numberb' 
                            FROM tr_tonase a 
                            WHERE a.`status` = 'A'
                            $date_sc $id_sc $percenta_sc $percentb_sc $numbera_sc $numberb_sc
                            ORDER BY a.created_date DESC");

            }else{

                $tr_tonase = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                            a.id_tonase AS 'tonaseid', a.id_route AS 'routeid',
                            a.tonase_percent_a AS 'percenta', a.tonase_a AS 'numbera', 
                            a.tonase_percent_b AS 'percentb', a.tonase_b AS 'numberb' 
                            FROM tr_tonase a 
                            WHERE a.`status` = 'A'
                            $id_sc $percenta_sc $percentb_sc $numbera_sc $numberb_sc
                            ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Tonase - '.date("d-m-Y").'', function($result) use ($tr_tonase, $date_start_format, $date_end_format) {

                $result->sheet('Data Tonase', function($sheet) use($tr_tonase,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $total = 0;
                    $count = 0;
                    foreach($tr_tonase as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->tonaseid,
                                $item->routeid,
                                $item->percenta,
                                $item->numbera,
                                $item->percentb,
                                $item->numberb
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','TONASE REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','ID','Route ID','Tonase Percent A %','Tonase Number A', 'Tonase Percent B %', 'Tonase Number B'));
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

            $arraydate = [$start,$end,$percenta,$numbera,$id,$percentb,$numberb];

            // return to view
            return view('operational/tonase/tonasehome', ['tr_tonase' => $tr_tonase, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage tonase-------------------------------------------------------------------

    // menu houling-------------------------------------------------------------------------

        // manage houling index function----------------------------------------------------
        public function index_houling()
        {
            // query left join
            $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

            $tr_houling = DB::table('tr_houling')
                        ->select('tr_houling.*', 'ms_mobil.mobil_id AS car_id', 'ms_karyawan.nama AS employee_name', 'lt_route.route_id AS route', 'lt_route.route_a AS route_a', 'lt_route.route_type_a AS route_type_a', 'lt_route.route_b AS route_b', 'lt_route.route_type_b AS route_type_b', 'lt_route.distance AS distance', 'lt_solar.id AS solar_id', 'lt_solar.name AS solar_name')
                        ->leftJoin('ms_mobil', 'tr_houling.mobil_id', '=', 'ms_mobil.mobil_id')
                        ->leftJoin('ms_karyawan', 'tr_houling.supir_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('lt_route', 'tr_houling.route_id', '=', 'lt_route.route_id')
                        ->leftJoin('lt_solar', 'tr_houling.solar_type_id', '=', 'lt_solar.id')
                        ->where('tr_houling.status', 'A')
                        ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Go to Houling';
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
            return view('operational/houling/houlinghome', ['tr_houling' => $tr_houling, 'lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage houling select2 car function----------------------------------------------
        public function search_car_houling()
        {
            $term = strip_tags(trim($_GET['q']));

            $ms_mobil = DB::table('ms_mobil')
                            ->where('status',"A")
                            ->where('type_car',1)
                            ->where('mobil_id','like', "%".$term."%" )
                            ->orWhere('customer_id','like', "%".$term."%" )
                            ->get();

            $query = $ms_mobil;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->mobil_id));
                    $new_row['text']=htmlentities(stripslashes($row->mobil_id ." - ". $row->customer_id));
                    $new_row['name']=htmlentities(stripslashes($row->customer_id));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage houling select2 employee function-----------------------------------------
        public function search_employee()
        {
            $term = strip_tags(trim($_GET['q']));

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
        }
        // ---------------------------------------------------------------------------------

        // manage houling select2 route function--------------------------------------------
        public function search_route()
        {
            $term = strip_tags(trim($_GET['q']));

            $lt_route = DB::table('lt_route')
                            ->where('status',"A")
                            ->where('route_id','like', "%".$term."%" )
                            ->orWhere('route_a','like', "%".$term."%" )
                            ->orWhere('route_b','like', "%".$term."%" )
                            ->get();

            $query = $lt_route;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->route_id));
                    $new_row['text']=htmlentities(stripslashes($row->route_id .' : '.$row->route_a." - ".$row->route_b));
                    $new_row['route_a']=htmlentities(stripslashes($row->route_a));
                    $new_row['route_b']=htmlentities(stripslashes($row->route_b));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage houling select2 tonase function-------------------------------------------
        public function search_tonase()
        {
            $term = strip_tags(trim($_GET['q']));

            $tr_tonase = DB::table('tr_tonase')
                            ->where('status',"A")
                            ->where('id_tonase','like', "%".$term."%" )
                            ->get();

            $query = $tr_tonase;

            if(sizeof($query) > 0){
                foreach ($query as $row){
                    $new_row['id']=htmlentities(stripslashes($row->id_tonase));
                    $new_row['text']=htmlentities(stripslashes($row->id_tonase));
                    $row_set[] = $new_row; //build an array
                }
                return json_encode($row_set); //format the array into json data
            }
        }
        // ---------------------------------------------------------------------------------

        // manage houling add function------------------------------------------------------
        public function add_houling()
        {
            $lt_solar = DB::table('lt_solar')
                            ->where('status',"A")
                            ->get();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Add Houling';
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

            return view('operational/houling/houlingadd',['lt_solar'=>$lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage houling do add function---------------------------------------------------
        public function do_add_houling(Request $request)
        {
            $this->validate($request, [
                'houling_date'        => 'required|max:20',
                'car_id'              => 'required|max:20',
                'employee_id'         => 'required|max:20',
                'route_id'            => 'required|max:20',
                'solar_type'          => 'required|max:10',
                'tonase_id'           => 'required|max:20'
            ]);

            // validate increment id
                $id = DB::table('tr_houling')->orderBy('id', 'desc')->first();
                if($id != null){
                    $lastnumber = substr($id->houling_id,3,7);
                    $next_id    = $lastnumber + 1;
                    $id         = "HOL".sprintf("%07d", $next_id);
                }else{
                    $id         = "HOL0000001";
                }
            // validate increment id

            $data = new manage_houling;
            $data->houling_id               = $id;
            $data->tanggal_houling          = $request->houling_date;
            $data->mobil_id                 = $request->car_id;
            $data->supir_id                 = $request->employee_id;
            $data->route_id                 = $request->route_id;
            $data->solar_type_id            = $request->solar_type;
            $data->tonase_id                = $request->tonase_id;
            $data->created_date             = date('Y-m-d H:i:s');
            $data->last_modify_date         = date('Y-m-d H:i:s');
            $data->modify_user_id           = Auth::user()->karyawan_id;
            $data->status                   = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Add New Houling'; 
                $table_logs  = 'tr_houling';
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

            return redirect(url('operational/houling_home'))->with('status', ' Add new houling has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage houling edit function-----------------------------------------------------
        public function edit_houling($id)
        {
            $lt_solar = DB::table('lt_solar')
                            ->where('status',"A")
                            ->get();   

            $tr_houling = DB::table('tr_houling')
                        ->select('tr_houling.*', 'ms_mobil.mobil_id AS car_id', 'ms_karyawan.nama AS employee_name', 'lt_route.route_id AS route', 'lt_route.route_a AS route_a', 'lt_route.route_type_a AS route_type_a', 'lt_route.route_b AS route_b', 'lt_route.route_type_b AS route_type_b', 'lt_route.distance AS distance', 'lt_solar.id AS solar_id', 'lt_solar.name AS name')
                        ->leftJoin('ms_mobil', 'tr_houling.mobil_id', '=', 'ms_mobil.mobil_id')
                        ->leftJoin('ms_karyawan', 'tr_houling.supir_id', '=', 'ms_karyawan.karyawan_id')
                        ->leftJoin('lt_route', 'tr_houling.route_id', '=', 'lt_route.route_id')
                        ->leftJoin('lt_solar', 'tr_houling.solar_type_id', '=', 'lt_solar.id')
                        ->where('tr_houling.status', 'A')
                        ->where('tr_houling.id', $id)
                        ->first();

            // save logs---------------------------------------------------
                $do_logs     = 'Open Edit Houling';
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

            return view('operational/houling/houlingedit', ['tr_houling' => $tr_houling,'lt_solar' => $lt_solar]);
        }
        // ---------------------------------------------------------------------------------

        // manage houling do edit function--------------------------------------------------
        public function do_edit_houling(Request $request, $id)
        {
            $this->validate($request, [
                'houling_date'        => 'required|max:20',
                'car_id'              => 'required|max:20',
                'employee_id'         => 'required|max:20',
                'route_id'            => 'required|max:20',
                'solar_type'          => 'required|max:10',
                'tonase_id'           => 'required|max:20'
            ]);

            $data = manage_houling::find($id);
            $data->tanggal_houling          = $request->houling_date;
            $data->mobil_id                 = $request->car_id;
            $data->supir_id                 = $request->employee_id;
            $data->route_id                 = $request->route_id;
            $data->solar_type_id            = $request->solar_type;
            $data->tonase_id                = $request->tonase_id;
            $data->last_modify_date         = date('Y-m-d H:i:s');
            $data->modify_user_id           = Auth::user()->karyawan_id;
            $data->status                   = 'A';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Edit New Houling'; 
                $table_logs  = 'tr_houling';
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

            return redirect(url('operational/houling_home'))->with('status', ' Updated houling has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage houling delete function---------------------------------------------------
        public function delete_houling($id)
        {
            $data = manage_houling::find($id);
            $data->deleted_date     = date('Y-m-d H:i:s');
            $data->modify_user_id   = Auth::user()->karyawan_id;
            $data->status           = 'D';
            $data->save();

            // save logs---------------------------------------------------
                $do_logs     = 'Do Delete Houling'; 
                $table_logs  = 'tr_houling';
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

            return redirect(url('operational/houling_home'))->with('status', ' Deleted houling has been success.');
        }
        // ---------------------------------------------------------------------------------

        // manage houling range function----------------------------------------------------
        public function houling_range(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $routeid        = $request->routeid;
            $id             = $request->houlingid;
            $carid          = $request->carid;
            $type           = $request->type;

            // validate empty
            if($start == "" && $end == "" && $id == "" && $routeid == "" &&  $carid == "" && $type == ""){    
                return redirect(url('operational/houling_home'));

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
                $id_sc          = $id != "" ? "AND a.houling_id LIKE '%".$id."%'" : "";
                $routeid_sc     = $routeid != "" ? "AND a.route_id LIKE '%".$routeid."%'" : "";
                $carid_sc       = $carid != "" ? "AND a.mobil_id LIKE '%".$carid."%'" : "";
                $type_sc        = $type != "" ? "AND a.solar_type_id = '".$type."'" : "";

                // query range
                $tr_houling = DB::select("SELECT a.*, b.mobil_id AS 'car_id', c.nama AS 'employee_name',
                                d.route_id AS 'route_id',
                                d.route_type_a AS 'route_type_a', d.route_a AS 'route_a',
                                d.route_type_b AS 'route_type_b', d.route_b AS 'route_b',
                                d.distance AS 'distance', e.id AS 'solar_id', e.name AS 'solar_name'
                                FROM tr_houling a LEFT JOIN ms_mobil b
                                ON a.mobil_id = b.mobil_id
                                LEFT JOIN ms_karyawan c
                                ON a.supir_id = c.karyawan_id
                                LEFT JOIN lt_route d
                                ON a.route_id = d.route_id
                                LEFT JOIN lt_solar e
                                ON a.solar_type_id = e.id
                                WHERE a.status = 'A'
                                $date_sc $id_sc $routeid_sc $carid_sc $type_sc
                                ORDER BY a.created_date DESC");

                $arraydate = [$date_start_format,$date_end_format,$routeid,$id,$carid,$type];

                 // query left join
                $lt_solar = DB::table('lt_solar')
                        ->where('status', 'A')
                        ->get();

                // save logs---------------------------------------------------
                    $do_logs     = 'Do Search Houling'; 
                    $table_logs  = 'tr_houling';
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
                return view('operational/houling/houlinghome')->with('tr_houling',$tr_houling)->with('arraydate',$arraydate)->with('lt_solar',$lt_solar);
            }
        }
        // ---------------------------------------------------------------------------------

        // manage houling export function---------------------------------------------------
        public function houling_export(Request $request)
        {
            //get date and get all entities
            $start          = $request->dateStart;
            $end            = $request->dateEnd;
            $routeid        = $request->routeid;
            $id             = $request->houlingid;
            $carid          = $request->carid;
            $type           = $request->type;

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
            $id_sc          = $id != "" ? "AND a.houling_id LIKE '%".$id."%'" : "";
            $routeid_sc     = $routeid != "" ? "AND a.route_id LIKE '%".$routeid."%'" : "";
            $carid_sc       = $carid != "" ? "AND a.mobil_id LIKE '%".$carid."%'" : "";
            $type_sc        = $type != "" ? "AND a.solar_type_id = '".$type."'" : "";

            // validate export date
            if($request->dateStart != ""){

                $tr_houling = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                                a.houling_id AS 'houlingid',
                                DATE_FORMAT(a.tanggal_houling,'%d/%m/%Y') AS 'houlingdate',
                                a.mobil_id AS 'carid', a.supir_id AS 'empid', a.route_id AS 'routeid',
                                a.solar_type_id AS 'solar_type_id', a.tonase_id AS 'tonaseid',
                                b.mobil_id AS 'car_id', c.nama AS 'employee_name',
                                d.route_id AS 'route_id',
                                d.route_type_a AS 'route_type_a', d.route_a AS 'route_a',
                                d.route_type_b AS 'route_type_b', d.route_b AS 'route_b',
                                d.distance AS 'distance', e.id AS 'solar_id', e.name AS 'solar_name'
                                FROM tr_houling a LEFT JOIN ms_mobil b
                                ON a.mobil_id = b.mobil_id
                                LEFT JOIN ms_karyawan c
                                ON a.supir_id = c.karyawan_id
                                LEFT JOIN lt_route d
                                ON a.route_id = d.route_id
                                LEFT JOIN lt_solar e
                                ON a.solar_type_id = e.id
                                WHERE a.status = 'A'
                                $date_sc $id_sc $routeid_sc $carid_sc $type_sc
                                ORDER BY a.created_date DESC");

            }else{

                $tr_houling = DB::select("SELECT DATE_FORMAT(a.created_date,'%d/%m/%Y') AS 'createddate',
                                a.houling_id AS 'houlingid',
                                DATE_FORMAT(a.tanggal_houling,'%d/%m/%Y') AS 'houlingdate',
                                a.mobil_id AS 'carid', a.supir_id AS 'empid', a.route_id AS 'routeid',
                                a.solar_type_id AS 'solar_type_id', a.tonase_id AS 'tonaseid',
                                b.mobil_id AS 'car_id', c.nama AS 'employee_name',
                                d.route_id AS 'route_id',
                                d.route_type_a AS 'route_type_a', d.route_a AS 'route_a',
                                d.route_type_b AS 'route_type_b', d.route_b AS 'route_b',
                                d.distance AS 'distance', e.id AS 'solar_id', e.name AS 'solar_name'
                                FROM tr_houling a LEFT JOIN ms_mobil b
                                ON a.mobil_id = b.mobil_id
                                LEFT JOIN ms_karyawan c
                                ON a.supir_id = c.karyawan_id
                                LEFT JOIN lt_route d
                                ON a.route_id = d.route_id
                                LEFT JOIN lt_solar e
                                ON a.solar_type_id = e.id
                                WHERE a.status = 'A'
                                $id_sc $routeid_sc $carid_sc $type_sc
                                ORDER BY a.created_date DESC");
            }
            // validate export date

            Excel::create('Data Houling - '.date("d-m-Y").'', function($result) use ($tr_houling, $date_start_format, $date_end_format) {

                $result->sheet('Data Houling', function($sheet) use($tr_houling,$date_start_format,$date_end_format)
                {
                    $i = 1;
                    $totalbuy = 0;
                    $count = 0;
                    foreach($tr_houling as $item){

                          $i++;
                          $count++;
                          $data=[];
                          array_push($data, array(
                                $item->createddate,
                                $item->houlingid,
                                $item->houlingdate,
                                $item->carid,
                                $item->empid,
                                $item->routeid,
                                $item->tonaseid,
                                $item->solar_name,
                                $item->distance
                            ));
                          $sheet->fromArray($data, null, 'A10', false, false);
                        }

                    // set manual sheet
                    $sheet->row(3, array('','TUNAS ABADI 8'));
                    $sheet->row(4, array('','HOULING REPORT'));
                    $sheet->row(6, array('Total Data : ',$count));
                    if($date_start_format != "1970-01-01"){
                        $sheet->row(7, array('Date Start : ',$date_start_format));
                        $sheet->row(8, array('Date End :',$date_end_format));
                    }else{
                        $sheet->row(7, array('Date Start : ','All Date'));
                        $sheet->row(8, array('Date End :','All Date'));
                    }

                    // set title
                    $sheet->row(9, array('Created Date','ID','Houling Date','Car ID','Driver ID', 'Route ID', 'Tonase ID', 'Solar Name', 'Distance'));
                    $sheet->setBorder('A9:I9', 'thin');

                    // set style column


                    $sheet->cells('A9:I9', function($cells){
                      $cells->setFontSize('13');
                      $cells->setValignment('center');
                    });

                    // merge cell
                    $sheet->mergeCells('A1:I1');
                    $sheet->cells('A1:I1', function($cells){
                        $cells->setFontSize('15');
                        $cells->setAlignment('center');
                    });

                    // loops value
                    for($k=9;$k<=$i+8;$k++){
                        $sheet->setBorder('A'.$k.':I'.$k, 'thin');
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

            $arraydate = [$start,$end,$routeid,$id,$carid,$type];

            // return to view
            return view('operational/Houling/houlinghome', ['tr_houling' => $tr_houling, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu houling-------------------------------------------------------------------------

}