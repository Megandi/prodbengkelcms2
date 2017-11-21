<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_logs extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'logs';

    //validate field
    protected $guarded = ['last_modify_date, modify_user_id, status'];
}
