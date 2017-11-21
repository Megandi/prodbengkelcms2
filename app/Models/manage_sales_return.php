<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_sales_return extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'tr_returpenjualan';

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

    public function returnpenjualan(){
      return $this->belongsTO('App\Models\manage_returnpenjualan', 'returpenjualan_id');
    }
}
