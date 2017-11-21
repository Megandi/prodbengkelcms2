<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_returnpembelian extends Model
{
     //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'ms_returpembelian';

    public function detail()
    {
      return $this->hasMany('App\Models\manage_purchase_return', 'returpembelian_id');
    }

    public function pembelian()
    {
      return $this->belongsTo('App\Models\manage_buying', 'pembelian_id');
    }

    

    //validate field
    //protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

}
