<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanupTableSetting extends Model
{
    protected $table = 'cleanup_table_settings';
    protected $connection = 'mysql_second';

    protected $fillable = [
        'table_name',
        'auto_delete_enabled',
        'retention_days',
        'last_cleanup_date',
    ];

    protected $casts = [
        'auto_delete_enabled' => 'boolean',
        'last_cleanup_date' => 'datetime',
    ];

    // Table names mapping
    const TABLES = [
        'entry_logs' => 'Entry Logs (Scanner History)',
        'visit_requests' => 'Visit Requests',
        'notifications' => 'Notifications',
        'shift_logs' => 'Shift Logs',
        'shifts' => 'Shift Assignments',
    ];

    /**
     * Get settings for a specific table
     */
    public static function getForTable($tableName)
    {
        return static::firstOrCreate(
            ['table_name' => $tableName],
            ['auto_delete_enabled' => true, 'retention_days' => 30]
        );
    }

    /**
     * Get all table settings
     */
    public static function getAllSettings()
    {
        $settings = [];
        foreach (self::TABLES as $table => $label) {
            $settings[$table] = [
                'label' => $label,
                'settings' => static::getForTable($table),
            ];
        }
        return $settings;
    }

    /**
     * Check if auto-delete is enabled for a table
     */
    public static function isAutoDeleteEnabled($tableName)
    {
        $settings = static::getForTable($tableName);
        return $settings->auto_delete_enabled;
    }

    /**
     * Get retention days for a table
     */
    public static function getRetentionDays($tableName)
    {
        $settings = static::getForTable($tableName);
        return $settings->retention_days;
    }

    /**
     * Update settings for a table
     */
    public static function updateSettings($tableName, $autoDeleteEnabled, $retentionDays)
    {
        $settings = static::getForTable($tableName);
        $settings->update([
            'auto_delete_enabled' => $autoDeleteEnabled,
            'retention_days' => $retentionDays,
        ]);
        return $settings;
    }

    /**
     * Update last cleanup date
     */
    public function updateLastCleanupDate()
    {
        $this->update(['last_cleanup_date' => now()]);
    }
}
