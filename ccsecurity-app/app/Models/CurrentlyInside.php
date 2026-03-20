<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentlyInside extends Model
{
    protected $table = 'currently_inside';
    protected $connection = 'mysql_second';
    public $timestamps = false;

    protected $fillable = [
        'qr_value',
        'user_type',
        'user_id',
        'fullname',
        'email',
        'role',
        'entered_at',
        'entry_log_id',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
    ];

    /**
     * Upsert a record (update if exists, insert if not)
     * This is more efficient than updateOrInsert for this use case
     */
    public static function trackEntry(array $unique, array $values): void
    {
        // Check if record exists
        $existing = static::where($unique)->first();
        
        if ($existing) {
            // Update existing record
            $existing->update($values);
        } else {
            // Insert new record
            static::create(array_merge($unique, $values));
        }
    }

    /**
     * Remove person from currently inside (on exit)
     */
    public static function trackExit(string $qrValue): void
    {
        static::where('qr_value', $qrValue)->delete();
    }

    /**
     * Get all people currently inside
     */
    public static function getAllInside()
    {
        return static::orderBy('entered_at', 'desc')->get();
    }

    /**
     * Get count of people currently inside
     */
    public static function getCount(): int
    {
        return static::count();
    }

    /**
     * Clear all records (use with caution - for maintenance only)
     */
    public static function clearAll(): void
    {
        static::truncate();
    }
}
