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
        'fullname',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'profile_picture',
        'status',
        'qr_value',
        'qr_status',
        'purpose_of_visit',
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
    ];

    // Status constants
    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

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
}
