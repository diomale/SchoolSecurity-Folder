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
        'fullname',
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

    /**
     * Get the entry logs scanned by this guard
     */
    public function entryLogs()
    {
        return $this->hasMany(EntryLog::class, 'security_guard_user_id');
    }

    /**
     * Get the shifts assigned to this guard
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'security_guard_user_id');
    }

    /**
     * Get the shift logs for this guard
     */
    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class, 'security_guard_user_id');
    }

    /**
     * Get the current active shift log (if any)
     */
    public function currentShiftLog()
    {
        return $this->hasOne(ShiftLog::class, 'security_guard_user_id')
                    ->whereNull('clock_out_time')
                    ->latest('id');
    }

    /**
     * Check if guard is currently on shift
     */
    public function isOnShift()
    {
        return $this->currentShiftLog()->exists();
    }

    /**
     * Get all entry logs scanned by this guard (for notifications)
     */
    public function allEntryLogs()
    {
        return $this->hasMany(EntryLog::class, 'security_guard_user_id');
    }
}
