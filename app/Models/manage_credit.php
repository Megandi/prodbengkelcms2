<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_credit extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_piutang';

    protected $fillable = [
        'bayar',
        'total'
    ];

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function penjualan()
    {
      return $this->belongsTO('App\Models\manage_selling', 'penjualan_id');
    }
}
