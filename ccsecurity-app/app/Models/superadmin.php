<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAdmin extends Authenticatable
{
    protected $table = 'super_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
