<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class manage_loan extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'lt_loan';

    //validate field
    protected $guarded = ['last_modify_date, deleted_date, modify_user_id, status'];
}
