<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    protected $table = 'shifts';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'security_guard_user_id',
        'shift_date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the security guard that owns the shift
     */
    public function securityGuardUser()
    {
        return $this->belongsTo(securityguard::class, 'security_guard_user_id');
    }

    /**
     * Get the shift logs for this shift
     */
    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class, 'shift_id');
    }

    /**
     * Scope for today's shifts
     */
    public function scopeToday($query)
    {
        return $query->where('shift_date', today());
    }

    /**
     * Scope for upcoming shifts
     */
    public function scopeUpcoming($query)
    {
        return $query->where('shift_date', '>=', today())
                     ->where('status', 'scheduled');
    }

    /**
     * Scope for past shifts
     */
    public function scopePast($query)
    {
        return $query->where('shift_date', '<', today());
    }
}
