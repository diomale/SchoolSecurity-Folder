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
        'created_at',
        'updated_at',
        'status',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'remember_token',
    ];

        protected $casts = [
        'password' => 'hashed',
    ];
}
