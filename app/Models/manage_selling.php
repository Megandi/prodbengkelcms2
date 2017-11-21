<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_selling extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'ms_penjualan';

    protected $fillable = [
        'detail_penjualan_id'
    ];

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function penjualan()
    {
      return $this->belongsTO('App\Models\manage_selling', 'penjualan_id');
    }

    public function customer()
    {
      return $this->belongsTO('App\Models\manage_customer', 'customer_id');
    }

    public function detail()
    {
      return $this->hasMany('App\Models\manage_selling_history', 'detail_penjualan_id');
    }

    public function karyawan()
    {
      return $this->belongsTO('App\Models\manage_karyawan', 'modify_user_id');
    }


}
