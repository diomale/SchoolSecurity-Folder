<?php

namespace App\Services;

use App\Models\SuperAdminActivityLog;
use Illuminate\Support\Facades\Auth;

class SuperAdminActivityLogger
{
    const CATEGORIES = [
        'authentication' => 'Authentication',
        'admin_management' => 'Admin Management',
        'system' => 'System',
        'other' => 'Other',
    ];

    public static function log(
        string $category,
        string $action,
        string $description,
        array $metadata = null,
        $superadminId = null,
        $superadminName = null
    ): SuperAdminActivityLog {
        if (!$superadminId) {
            $admin = Auth::guard('superadmin')->user();
            $superadminId = $admin?->id;
            $superadminName = $admin?->name ?? $admin?->email ?? 'Unknown';
        }

        $request = request();

        return SuperAdminActivityLog::create([
            'superadmin_id' => $superadminId ?? 0,
            'superadmin_name' => $superadminName ?? 'System',
            'category' => $category,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? substr($request->userAgent(), 0, 500) : null,
            'created_at' => now(),
        ]);
    }

    public static function auth(string $action, string $description, array $metadata = null): SuperAdminActivityLog
    {
        return static::log('authentication', $action, $description, $metadata);
    }

    public static function adminManagement(string $action, string $description, array $metadata = null): SuperAdminActivityLog
    {
        return static::log('admin_management', $action, $description, $metadata);
    }

    public static function system(string $action, string $description, array $metadata = null): SuperAdminActivityLog
    {
        return static::log('system', $action, $description, $metadata);
    }

    public static function cleanupOldLogs(int $daysToKeep = 30): int
    {
        return SuperAdminActivityLog::where('created_at', '<', now()->subDays($daysToKeep))->delete();
    }
}
