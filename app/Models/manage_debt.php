<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_debt extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_hutang';

    protected $fillable = [
        'bayar'
    ];


    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];

    public function pembelian()
    {
      return $this->belongsTO('App\Models\manage_buying', 'pembelian_id');
    }
}
