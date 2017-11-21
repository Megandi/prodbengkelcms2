<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_returnpenjualan extends Model
{
     //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'ms_returpenjualan';

    public function detail()
    {
      return $this->hasMany('App\Models\manage_sales_return', 'returpenjualan_id');
    }

    public function penjualan()
    {
      return $this->belongsTo('App\Models\manage_selling', 'penjualan_id');
    }

    //validate field
    //protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

}
