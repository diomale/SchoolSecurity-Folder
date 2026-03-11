<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanupSetting extends Model
{
    protected $table = 'cleanup_settings';
    protected $connection = 'mysql_second';

    protected $fillable = [
        'auto_delete_enabled',
        'retention_days',
        'last_cleanup_date',
    ];

    protected $casts = [
        'auto_delete_enabled' => 'boolean',
        'last_cleanup_date' => 'datetime',
    ];

    /**
     * Get the single cleanup settings record
     */
    public static function getInstance()
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['auto_delete_enabled' => true, 'retention_days' => 30]
        );
    }

    /**
     * Check if auto-delete is enabled
     */
    public static function isAutoDeleteEnabled()
    {
        $settings = static::getInstance();
        return $settings->auto_delete_enabled;
    }

    /**
     * Enable auto-delete
     */
    public static function enableAutoDelete()
    {
        $settings = static::getInstance();
        $settings->update(['auto_delete_enabled' => true]);
    }

    /**
     * Disable auto-delete
     */
    public static function disableAutoDelete()
    {
        $settings = static::getInstance();
        $settings->update(['auto_delete_enabled' => false]);
    }

    /**
     * Toggle auto-delete status
     */
    public static function toggleAutoDelete()
    {
        $settings = static::getInstance();
        $settings->update(['auto_delete_enabled' => !$settings->auto_delete_enabled]);
        return $settings->auto_delete_enabled;
    }

    /**
     * Update retention days
     */
    public static function updateRetentionDays($days)
    {
        $settings = static::getInstance();
        $settings->update(['retention_days' => $days]);
    }

    /**
     * Update last cleanup date
     */
    public function updateLastCleanupDate()
    {
        $this->update(['last_cleanup_date' => now()]);
    }
}
