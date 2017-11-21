<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_biayalain_detail extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_biayalain_detail';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];


}
