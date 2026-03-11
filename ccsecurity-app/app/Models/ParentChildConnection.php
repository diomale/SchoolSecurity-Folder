<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentChildConnection extends Model
{
    use HasFactory;

    protected $table = 'parent_child_connections';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';

    protected $fillable = [
        'outside_user_id',
        'inside_user_id',
        'relationship',
        'status',
        'admin_remarks',
        'approved_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the outside user (parent/visitor) who requested the connection
     */
    public function outsideUser()
    {
        return $this->belongsTo(OutsideUser::class, 'outside_user_id');
    }

    /**
     * Get the inside user (child/student) being connected to
     */
    public function insideUser()
    {
        return $this->belongsTo(InsideUser::class, 'inside_user_id');
    }

    /**
     * Approve the connection
     */
    public function approve($remarks = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'admin_remarks' => $remarks,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the connection
     */
    public function reject($remarks = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_remarks' => $remarks,
        ]);
    }

    /**
     * Check if connection is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if connection is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if connection is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
