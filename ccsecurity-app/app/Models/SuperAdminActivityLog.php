<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminActivityLog extends Model
{
    protected $table = 'superadmin_activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'superadmin_id',
        'superadmin_name',
        'category',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
