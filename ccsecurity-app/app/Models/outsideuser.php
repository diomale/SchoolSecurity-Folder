<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutsideUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'outside_user';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'fullname',
        'email',
        'phone_number',
        'password',
        'profile_picture',
        'status',
        'email_verified_at',
        'email_verification_token',
        'qr_value',
        'qr_status',
        'qr_expires_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime:Y-m-d h:i A',
        'updated_at' => 'datetime:Y-m-d h:i A',
        'qr_expires_at' => 'datetime:Y-m-d h:i A',
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->fullname)) {
                $user->fullname = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty(['first_name', 'last_name'])) {
                $user->fullname = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            }
        });
    }

    // Status constants
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the visit requests for this outside user
     */
    public function visitRequests()
    {
        return $this->hasMany(VisitRequest::class, 'outside_user_id');
    }

    /**
     * Get pending visit requests
     */
    public function pendingVisitRequests()
    {
        return $this->hasMany(VisitRequest::class, 'outside_user_id')
                    ->where('status', 'pending');
    }

    /**
     * Get parent-child connections where this user is the parent/visitor
     */
    public function parentChildConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'outside_user_id');
    }

    /**
     * Get pending connection requests
     */
    public function pendingConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'outside_user_id')
                    ->where('status', ParentChildConnection::STATUS_PENDING);
    }

    /**
     * Get approved connections only
     */
    public function approvedConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'outside_user_id')
                    ->where('status', ParentChildConnection::STATUS_APPROVED);
    }

    /**
     * Get connected inside users (children) through approved connections
     */
    public function connectedChildren()
    {
        return $this->belongsToMany(InsideUser::class, 'parent_child_connections', 'outside_user_id', 'inside_user_id')
                    ->wherePivot('status', ParentChildConnection::STATUS_APPROVED)
                    ->withPivot('relationship', 'approved_at');
    }
}
