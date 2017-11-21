<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_selling_history extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'tr_detail_penjualan';

    protected $fillable = [
        'type_sell'
    ];

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function barang(){
      return $this->belongsTO('App\Models\manage_items', 'barang_id');
    }

    public function barang_temp(){
      return $this->belongsTO('App\Models\manage_items_temp', 'barang_id');
    }

    public function jasa(){
      return $this->belongsTO('App\Models\manage_service', 'barang_id');
    }

    public function karyawan1(){
      return $this->belongsTO('App\Models\manage_karyawan', 'id_karyawan_kerja1');
    }

    public function karyawan2(){
      return $this->belongsTO('App\Models\manage_karyawan', 'id_karyawan_kerja2');
    }

    public function karyawan3(){
      return $this->belongsTO('App\Models\manage_karyawan', 'id_karyawan_kerja3');
    }

    public function karyawan4(){
      return $this->belongsTO('App\Models\manage_karyawan', 'id_karyawan_kerja4');
    }

    public function karyawan5(){
      return $this->belongsTO('App\Models\manage_karyawan', 'id_karyawan_kerja5');
    }
}
