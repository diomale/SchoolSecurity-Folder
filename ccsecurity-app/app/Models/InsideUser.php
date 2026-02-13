<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsideUser extends Model
{
    protected $table = 'inside_user';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'role',
        'fullname',
        'password',
        'first_name',
        'last_name',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}
