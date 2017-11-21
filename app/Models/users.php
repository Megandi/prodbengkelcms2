<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    //protected timestamp laravel
    public $timestamps = false;

    protected $table = 'users';

    //validate field
    protected $guarded = ['updated_at, modify_user_id, status, remember_token'];
}
