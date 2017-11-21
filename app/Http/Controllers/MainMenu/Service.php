<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_items;
use App\Models\manage_service;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Service extends Controller
{
	function __construct(Request $request)
    {
        $this->middleware('auth');
    }  

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
            return view('mainmenu/service/servicehome', ['ms_jasa' => $ms_jasa]);
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

            return view('mainmenu/service/serviceadd');
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

            return redirect(url('mainmenu/service_home'))->with('status', ' Created new service has been success.');
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

            return view('mainmenu/service/serviceedit', ['ms_jasa' => $ms_jasa]);
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

            return redirect(url('mainmenu/service_home'))->with('status', ' Updated service has been success.');
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

            return redirect(url('mainmenu/service_home'))->with('status', ' Deleted service has been success.');
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
                return redirect(url('mainmenu/service_home'));

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
                return view('mainmenu/service/servicehome')->with('ms_jasa',$ms_jasa)->with('arraydate',$arraydate);
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
            return view('mainmenu/service/servicehome', ['ms_jasa' => $ms_jasa, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage service------------------------------------------------------------------
}
