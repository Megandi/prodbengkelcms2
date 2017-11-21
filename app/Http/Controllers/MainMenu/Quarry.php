<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_quarry;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Quarry extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('mainmenu/quarry/quarhome', ['ms_tambang' => $ms_tambang]);
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

            return view('mainmenu/quarry/quaradd');
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

            return redirect(url('mainmenu/quar_home'))->with('status', ' Add new quarry has been success.');
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

            return view('mainmenu/quarry/quaredit', ['ms_tambang' => $ms_tambang]);
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

            return redirect(url('mainmenu/quar_home'))->with('status', ' Updated quarry has been success.');
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

            return redirect(url('mainmenu/quar_home'))->with('status', ' Deleted quarry has been success.');
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
               
                return redirect(url('mainmenu/quar_home'));

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
                return view('mainmenu/quarry/quarhome')->with('ms_tambang',$ms_tambang)->with('arraydate',$arraydate);
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
            return view('mainmenu/quarry/quarhome', ['ms_tambang' => $ms_tambang, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage quar---------------------------------------------------------------------
}
