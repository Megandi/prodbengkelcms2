<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_houling extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'tr_houling';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];
}
