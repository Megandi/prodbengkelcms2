<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_karyawan extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'ms_karyawan';

    protected $primaryKey = 'karyawan_id';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function jabatan()
    {
      return $this->belongsTO('App\Models\manage_position', 'jabatan');
    }
}
