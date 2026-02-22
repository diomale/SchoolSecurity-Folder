<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class securityguard extends Authenticatable
{
    protected $table = 'security_guard_user';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'created_at',
        'updated_at',
        'status'
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'password' => 'hashed',
        'updated_at' => 'datetime:Y-m-d h:i A',
    ];
}
