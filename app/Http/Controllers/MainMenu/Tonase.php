<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_tonase;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Tonase extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('mainmenu/tonase/tonasehome', ['tr_tonase' => $tr_tonase]);
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


            return view('mainmenu/tonase/tonaseadd');
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

            return redirect(url('mainmenu/tonase_home'))->with('status', ' Created new tonase has been success.');
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

            return view('mainmenu/tonase/tonaseedit', ['tr_tonase' => $tr_tonase]);
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

            return redirect(url('mainmenu/tonase_home'))->with('status', ' Updated tonase has been success.');
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

            return redirect(url('mainmenu/tonase_home'))->with('status', ' Deleted tonase has been success.');
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
               
                return redirect(url('mainmenu/tonase_home'));

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
                return view('mainmenu/tonase/tonasehome')->with('tr_tonase',$tr_tonase)->with('arraydate',$arraydate);
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
            return view('mainmenu/tonase/tonasehome', ['tr_tonase' => $tr_tonase, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage tonase-------------------------------------------------------------------
}
