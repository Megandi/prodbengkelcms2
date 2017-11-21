<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_level extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_user_type';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];
}
