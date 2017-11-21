<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_level_akses extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'level_akses';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];
}
