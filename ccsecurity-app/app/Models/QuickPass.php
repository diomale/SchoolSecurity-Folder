<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class QuickPass extends Model
{
    use HasFactory;

    protected $table = 'quick_passes';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    // Disable timestamps since table doesn't have updated_at
    public $timestamps = false;

    protected $fillable = [
        'visitor_name',
        'vehicle_plate',
        'purpose',
        'qr_value',
        'valid_date',
        'expires_at',
        'status',
        'created_by_guard_id',
        'deleted_at',
    ];

    protected $casts = [
        'valid_date' => 'date',
        'expires_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    /**
     * Boot the model and set default values
     */
    protected static function booted()
    {
        static::creating(function ($quickPass) {
            // Set default valid_date to today if not set
            if (empty($quickPass->valid_date)) {
                $quickPass->valid_date = Carbon::today();
            }

            // Set default expires_at to end of day if not set
            if (empty($quickPass->expires_at)) {
                $quickPass->expires_at = Carbon::today()->endOfDay();
            }

            // Set default status
            if (empty($quickPass->status)) {
                $quickPass->status = self::STATUS_ACTIVE;
            }
        });
    }

    /**
     * Get the security guard who created this quick pass
     */
    public function securityGuard()
    {
        return $this->belongsTo(SecurityGuard::class, 'created_by_guard_id');
    }

    /**
     * Get entry logs for this quick pass
     */
    public function entryLogs()
    {
        return $this->hasMany(EntryLog::class, 'quick_pass_id');
    }

    /**
     * Scope to get only active quick passes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get quick passes valid for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('valid_date', $date);
    }

    /**
     * Scope to get today's quick passes
     */
    public function scopeToday($query)
    {
        return $query->where('valid_date', Carbon::today());
    }

    /**
     * Scope to get only non-deleted records
     */
    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Check if the quick pass is expired
     */
    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Check if the quick pass is still valid
     */
    public function isValid()
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    /**
     * Mark the quick pass as used
     */
    public function markAsUsed()
    {
        $this->update(['status' => self::STATUS_USED]);
    }

    /**
     * Mark the quick pass as expired
     */
    public function markAsExpired()
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Get the purpose badge color
     */
    public function getPurposeColorAttribute()
    {
        $colors = [
            'Delivery' => '#ffc107',
            'Meeting' => '#007bff',
            'Parent' => '#28a745',
            'Contractor' => '#fd7e14',
            'Other' => '#6c757d',
        ];

        return $colors[$this->purpose] ?? '#6c757d';
    }
}
