<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_items;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Items extends Controller
{
	function __construct(Request $request)
    {
        $this->middleware('auth');
    }    

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
            return view('mainmenu/items/itemshome', ['ms_barang' => $ms_barang, 'ms_kategori_barang' => $ms_kategori_barang]);
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

            return view('mainmenu/items/itemsadd',['ms_kategori_barang'=>$ms_kategori_barang]);
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

            return redirect(url('mainmenu/items_home'))->with('status', ' Created new items has been success.');
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

            return view('mainmenu/items/itemsedit', ['ms_barang' => $ms_barang, 'ms_kategori_barang' => $ms_kategori_barang]);
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

            return redirect(url('mainmenu/items_home'))->with('status', ' Updated items has been success.');
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

            return redirect(url('mainmenu/items_home'))->with('status', ' Deleted items has been success.');
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
                return redirect(url('mainmenu/items_home'));

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
                return view('mainmenu/items/itemshome')->with('ms_barang',$ms_barang)->with('arraydate',$arraydate)->with('ms_kategori_barang',$ms_kategori_barang);
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
            return view('mainmenu/items/itemshome', ['ms_barang' => $ms_barang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage items--------------------------------------------------------------------
}
