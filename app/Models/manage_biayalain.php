<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_biayalain extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_biayalain';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function detail()
    {
      return $this->hasMany('App\Models\manage_biayalain_detail', 'biayalain_id');
    }

    // public function karyawan()
    // {
    //   return $this->belongsTO('App\Models\manage_karyawan', 'modify_user_id');
    // }
    //
    // public function supplier()
    // {
    //   return $this->belongsTO('App\Models\manage_supplier', 'supplier_id');
    // }

}
