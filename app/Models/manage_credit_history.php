<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_credit_history extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'tr_credit_history';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];
}
