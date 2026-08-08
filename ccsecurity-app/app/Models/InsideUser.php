<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\EntryLog;
use App\Models\ParentChildConnection;
use App\Models\OutsideUser;

class InsideUser extends Authenticatable
{
    protected $table = 'inside_user';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'role',
        'fullname',
        'password',
        'first_name',
        'last_name',
        'email',
        'created_at',
        'updated_at',
        'status',
        'qr_value',
        'qr_status',
        'can_create_events',
        'terms_accepted_at',
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
    protected static function booted()
    {
        static::created(function ($user) {
            // Logic: Prefix + unique random token (avoids guessable/sequential QR codes)
            $user->qr_value = 'User-' . strtoupper(bin2hex(random_bytes(8)));

            // Save without triggering events again to avoid infinite loops
            $user->saveQuietly();
        });
    }

    /**
     * Get parent-child connections where this user is the child/student
     */
    public function parentChildConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'inside_user_id');
    }

    /**
     * Get pending connection requests
     */
    public function pendingConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'inside_user_id')
                    ->where('status', ParentChildConnection::STATUS_PENDING);
    }

    /**
     * Get approved connections only
     */
    public function approvedConnections()
    {
        return $this->hasMany(ParentChildConnection::class, 'inside_user_id')
                    ->where('status', ParentChildConnection::STATUS_APPROVED);
    }

    /**
     * Get connected outside users (parents) through approved connections
     */
    public function connectedParents()
    {
        return $this->belongsToMany(OutsideUser::class, 'parent_child_connections', 'inside_user_id', 'outside_user_id')
                    ->wherePivot('status', ParentChildConnection::STATUS_APPROVED)
                    ->withPivot('relationship', 'approved_at');
    }

    /**
     * Get all entry logs for this inside user
     */
    public function entryLogs()
    {
        return $this->hasMany(EntryLog::class, 'inside_user_id');
    }

    /**
     * Get trusted devices for this user
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class, 'inside_user_id');
    }
}
