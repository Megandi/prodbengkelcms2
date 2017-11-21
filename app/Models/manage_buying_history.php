<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_buying_history extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'tr_detail_pembelian';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function barang(){
      return $this->belongsTO('App\Models\manage_items', 'barang_id');
    }

    public function barang_temp()
    {
      return $this->belongsTO('App\Models\manage_items_temp', 'barang_id');
    }


}
