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
    ];

    protected $casts = [
        'qr_downloaded' => 'boolean',
        'qr_emailed' => 'boolean',
        'qr_downloaded_at' => 'datetime',
        'qr_emailed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_REGISTERED = 'registered';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';

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
}
