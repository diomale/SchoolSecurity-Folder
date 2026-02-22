<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsideUser extends Authenticatable
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
        'created_at',
        'updated_at',
        'status',
        'qr_value',
        'qr_status',
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
    protected static function booted()
    {
        static::created(function ($user) {
            // Logic: Prefix + (Base Number + Auto-increment ID)
            $user->qr_value = 'User-' . (1000 + $user->id);
            
            // Save without triggering events again to avoid infinite loops
            $user->saveQuietly();
        });
    }
}
