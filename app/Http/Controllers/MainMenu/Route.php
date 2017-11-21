<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_route;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Route extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('mainmenu/route/routehome', ['lt_route' => $lt_route]);
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

            return view('mainmenu/route/routeadd');
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

            return redirect(url('mainmenu/route_home'))->with('status', ' Add new route has been success.');
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

            return view('mainmenu/route/routeedit', ['lt_route' => $lt_route]);
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

            return redirect(url('mainmenu/route_home'))->with('status', ' Updated route has been success.');
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

            return redirect(url('mainmenu/route_home'))->with('status', ' Deleted route has been success.');
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
               
                return redirect(url('mainmenu/route_home'));

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
                return view('mainmenu/route/routehome')->with('lt_route',$lt_route)->with('arraydate',$arraydate);
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
            return view('mainmenu/route/routehome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage route--------------------------------------------------------------------
}
