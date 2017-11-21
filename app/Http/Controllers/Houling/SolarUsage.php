<?php

namespace App\Http\Controllers\Houling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_solar;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class SolarUsage extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('houling/solarusage/solarhome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'lt_solar' => $lt_solar]);
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

            return view('houling/solarusage/solaradd',['lt_solar'=>$lt_solar]);
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

            return redirect(url('houling/solar_home'))->with('status', ' Add new solar has been success.');
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

            return view('houling/solarusage/solaredit', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'lt_solar' => $lt_solar]);
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


            return redirect(url('houling/solar_home'))->with('status', ' Updated solar has been success.');
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

            return redirect(url('houling/solar_home'))->with('status', ' Deleted solar has been success.');
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
                
                return redirect(url('houling/solar_home'));

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
                return view('houling/solarusage/solarhome')->with('lt_pemakaiansolar',$lt_pemakaiansolar)->with('arraydate',$arraydate)->with('lt_solar',$lt_solar);
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
            return view('houling/solarusage/solarhome', ['lt_pemakaiansolar' => $lt_pemakaiansolar, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu solar usage---------------------------------------------------------------------
}
