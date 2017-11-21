<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_type_items;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class TypeItems extends Controller
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
            return view('mainmenu/type_items/typehome', ['ms_kategori_barang' => $ms_kategori_barang]);
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

            return view('mainmenu/type_items/typeadd');
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

            return redirect(url('mainmenu/items_type_home'))->with('status', ' Created new type items has been success.');
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

            return view('mainmenu/type_items/typeedit', ['ms_kategori_barang' => $ms_kategori_barang]);
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

            return redirect(url('mainmenu/items_type_home'))->with('status', ' Updated type items has been success.');
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

            return redirect(url('mainmenu/items_type_home'))->with('status', ' Deleted type items has been success.');
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
                
                return redirect(url('mainmenu/items_type_home'));

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
                return view('mainmenu/type_items/typehome')->with('ms_kategori_barang',$ms_kategori_barang)->with('arraydate',$arraydate);
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
            return view('mainmenu/type_items/typehome', ['ms_kategori_barang' => $ms_kategori_barang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage type items---------------------------------------------------------------
}
