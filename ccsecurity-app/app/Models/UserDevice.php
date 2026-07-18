<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';
    protected $table = 'user_devices';

    protected $fillable = [
        'inside_user_id',
        'device_fingerprint',
        'ip_address',
        'user_agent',
        'browser',
        'os',
        'is_trusted',
        'last_used_at',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function insideUser()
    {
        return $this->belongsTo(InsideUser::class);
    }
}
