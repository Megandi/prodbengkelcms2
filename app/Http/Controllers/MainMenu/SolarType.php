<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_solar_type;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
class SolarType extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('mainmenu/solar_type/solartypehome', ['lt_solar' => $lt_solar]);
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

            return view('mainmenu/solar_type/solartypeadd');
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

            return redirect(url('mainmenu/solar_type_home'))->with('status', ' Add new solar type has been success.');
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

            return view('mainmenu/solar_type/solartypeedit', ['lt_solar' => $lt_solar]);
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

            return redirect(url('mainmenu/solar_type_home'))->with('status', ' Updated solar type has been success.');
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

            return redirect(url('mainmenu/solar_type_home'))->with('status', ' Deleted solar type has been success.');
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
               
                return redirect(url('mainmenu/solar_type_home'));

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
                return view('mainmenu/solar_type/solartypehome')->with('lt_solar',$lt_solar)->with('arraydate',$arraydate);
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
            return view('mainmenu/solar_type/solartypehome', ['lt_solar' => $lt_solar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage solar--------------------------------------------------------------------
}
