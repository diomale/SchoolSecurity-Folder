<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShiftLog extends Model
{
    protected $table = 'shift_logs';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'security_guard_user_id',
        'shift_id',
        'clock_in_time',
        'clock_out_time',
        'handover_note',
        'status',
    ];

    protected $casts = [
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the security guard that owns the shift log
     */
    public function securityGuardUser()
    {
        return $this->belongsTo(securityguard::class, 'security_guard_user_id');
    }

    /**
     * Get the shift associated with this log
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Get the duration of the shift in hours
     */
    public function getDurationAttribute()
    {
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return null;
        }

        return $this->clock_in_time->diffInHours($this->clock_out_time);
    }

    /**
     * Scope for active shift logs (clocked in but not out)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('clock_in_time')
                     ->whereNull('clock_out_time');
    }

    /**
     * Scope for completed shift logs
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('clock_out_time');
    }

    /**
     * Scope for today's shift logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('clock_in_time', today());
    }
}
