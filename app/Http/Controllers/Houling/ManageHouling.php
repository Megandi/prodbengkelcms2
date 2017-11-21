<?php

namespace App\Http\Controllers\Houling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_houling;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ManageHouling extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('houling/houling/houlinghome', ['tr_houling' => $tr_houling, 'lt_solar' => $lt_solar]);
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

            return view('houling/houling/houlingadd',['lt_solar'=>$lt_solar]);
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

            return redirect(url('houling/houling_home'))->with('status', ' Add new houling has been success.');
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

            return view('houling/houling/houlingedit', ['tr_houling' => $tr_houling,'lt_solar' => $lt_solar]);
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

            return redirect(url('houling/houling_home'))->with('status', ' Updated houling has been success.');
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

            return redirect(url('houling/houling_home'))->with('status', ' Deleted houling has been success.');
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
                return redirect(url('houling/houling_home'));

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
                return view('houling/houling/houlinghome')->with('tr_houling',$tr_houling)->with('arraydate',$arraydate)->with('lt_solar',$lt_solar);
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
            return view('houling/Houling/houlinghome', ['tr_houling' => $tr_houling, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu houling-------------------------------------------------------------------------
}
