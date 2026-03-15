<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'event_id',
        'outside_user_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'qr_code',
        'qr_downloaded',
        'qr_downloaded_at',
        'qr_emailed',
        'qr_emailed_at',
        'status',
        'checked_in_at',
        'checked_out_at',
        'needs_creator_approval',
        'creator_approved_at',
    ];

    protected $casts = [
        'qr_downloaded' => 'boolean',
        'qr_emailed' => 'boolean',
        'needs_creator_approval' => 'boolean',
        'qr_downloaded_at' => 'datetime',
        'qr_emailed_at' => 'datetime',
        'creator_approved_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_REGISTERED = 'registered';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';

    // Approval status constants
    const APPROVAL_PENDING = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';

    /**
     * Get the event this registration belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the outside user (if registered)
     */
    public function outsideUser()
    {
        return $this->belongsTo(OutsideUser::class, 'outside_user_id');
    }

    /**
     * Scope for registered participants
     */
    public function scopeRegistered($query)
    {
        return $query->where('status', self::STATUS_REGISTERED);
    }

    /**
     * Scope for checked in participants
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN);
    }

    /**
     * Generate unique QR code for registration
     */
    public static function generateQRCode($eventId)
    {
        return 'EVT' . $eventId . '-' . strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Get full name attribute
     */
    public function getFullnameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Scope for registrations pending creator approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('needs_creator_approval', true)
                     ->whereNull('creator_approved_at');
    }

    /**
     * Scope for approved registrations by creator
     */
    public function scopeCreatorApproved($query)
    {
        return $query->where('needs_creator_approval', false)
                     ->orWhereNotNull('creator_approved_at');
    }

    /**
     * Check if registration needs creator approval
     */
    public function needsApproval()
    {
        return $this->needs_creator_approval && !$this->creator_approved_at;
    }

    /**
     * Check if registration is approved by creator
     */
    public function isApprovedByCreator()
    {
        return !$this->needs_creator_approval || $this->creator_approved_at !== null;
    }

    /**
     * Mark registration as approved by creator
     */
    public function approveByCreator()
    {
        $this->update([
            'needs_creator_approval' => false,
            'creator_approved_at' => now(),
        ]);
    }

    /**
     * Mark registration as rejected by creator
     */
    public function rejectByCreator()
    {
        // Keep needs_creator_approval true but can add a rejection note field if needed
        $this->update([
            'needs_creator_approval' => true,
            // Add a status field for rejection if needed
        ]);
    }
}
