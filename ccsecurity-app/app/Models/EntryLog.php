<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntryLog extends Model
{

    protected $table = 'Entry_logs';
    protected $connection = 'mysql_second';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'inside_user_id',
        'outside_user_id',
        'quick_pass_id',
        'qr_value',
        'security_guard_user_id',
        'scan_at',
        'scan_type',
    ];


    protected $casts = [
        'scan_at' => 'datetime',
    ];

    /**
     * Get the inside user that owns the entry log
     */
    public function insideUser()
    {
        return $this->belongsTo(InsideUser::class, 'inside_user_id');
    }

    /**
     * Get the outside user that owns the entry log
     */
    public function outsideUser()
    {
        return $this->belongsTo(OutsideUser::class, 'outside_user_id');
    }

    /**
     * Get the quick pass associated with this entry log
     */
    public function quickPass()
    {
        return $this->belongsTo(QuickPass::class, 'quick_pass_id');
    }

    /**
     * Get the security guard user that scanned the QR
     */
    public function securityGuardUser()
    {
        return $this->belongsTo(securityguard::class, 'security_guard_user_id', 'id');
    }
}