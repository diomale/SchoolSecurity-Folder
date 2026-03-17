<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inside_user_id',
        'event_name',
        'event_description',
        'event_date',
        'event_start_time',
        'event_end_time',
        'qr_request_deadline',
        'alien_user_limit',
        'status',
        'admin_remarks',
        'approved_at',
        'show_on_welcome',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_start_time' => 'datetime:H:i',
        'event_end_time' => 'datetime:H:i',
        'qr_request_deadline' => 'datetime:Y-m-d H:i:s',
        'approved_at' => 'datetime',
        'show_on_welcome' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the inside user who created the event
     */
    public function insideUser()
    {
        return $this->belongsTo(InsideUser::class, 'inside_user_id');
    }

    /**
     * Get all registrations for this event
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    /**
     * Get registrations pending creator approval
     */
    public function pendingApprovals()
    {
        return $this->hasMany(EventRegistration::class, 'event_id')
                    ->pendingApproval();
    }

    /**
     * Get approved registrations by creator
     */
    public function approvedRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id')
                    ->creatorApproved();
    }

    /**
     * Scope for pending events
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved events
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    /**
     * Scope for events shown on welcome page
     */
    public function scopePublicVisible($query)
    {
        return $query->where('show_on_welcome', true)
                     ->where('status', self::STATUS_APPROVED)
                     ->where('event_date', '>=', now()->toDateString());
    }

    /**
     * Get registration count
     */
    public function getRegistrationCountAttribute()
    {
        return $this->registrations()->count();
    }

    /**
     * Check if event is full
     */
    public function getIsFullAttribute()
    {
        return $this->registrations()->count() >= $this->alien_user_limit;
    }

    /**
     * Check if registration is still open
     */
    public function getIsRegistrationOpenAttribute()
    {
        return now()->lessThan($this->qr_request_deadline) && !$this->is_full;
    }
}
