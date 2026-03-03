<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
{
    protected $table = 'visit_requests';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'outside_user_id',
        'visit_date',
        'visit_time',
        'purpose',
        'person_to_meet',
        'status',
        'admin_remarks',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the outside user that owns the visit request
     */
    public function outsideUser()
    {
        return $this->belongsTo(OutsideUser::class, 'outside_user_id');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for today's visits
     */
    public function scopeToday($query)
    {
        return $query->where('visit_date', today());
    }
}
