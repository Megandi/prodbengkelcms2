<?php

namespace App\Http\Controllers\MainMenu;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\manage_port;

use App\Models\manage_logs;

use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;

class Port extends Controller
{
    function __construct(Request $request)
    {
        $this->middleware('auth');
    }

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
            return view('mainmenu/port/porthome', ['ms_pelabuhan' => $ms_pelabuhan]);
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

            return view('mainmenu/port/portadd');
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

            return redirect(url('mainmenu/port_home'))->with('status', ' Add new port has been success.');
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

            return view('mainmenu/port/portedit', ['ms_pelabuhan' => $ms_pelabuhan]);
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

            return redirect(url('mainmenu/port_home'))->with('status', ' Updated port has been success.');
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

            return redirect(url('mainmenu/port_home'))->with('status', ' Deleted port has been success.');
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
               
                return redirect(url('mainmenu/port_home'));

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
                return view('mainmenu/port/porthome')->with('ms_pelabuhan',$ms_pelabuhan)->with('arraydate',$arraydate);
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
            return view('mainmenu/port/porthome', ['ms_pelabuhan' => $ms_pelabuhan, 'arraydate' => $arraydate]);
        }
        // ---------------------------------------------------------------------------------

    // menu manage port---------------------------------------------------------------------
}
