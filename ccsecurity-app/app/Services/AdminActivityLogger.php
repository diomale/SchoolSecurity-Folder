<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    const CATEGORIES = [
        'authentication' => 'Authentication',
        'user_management' => 'User Management',
        'event_management' => 'Event Management',
        'qr_management' => 'QR Management',
        'shift_management' => 'Shift Management',
        'visit_requests' => 'Visit Requests',
        'connections' => 'Connections',
        'system' => 'System',
        'other' => 'Other',
    ];

    public static function log(
        string $category,
        string $action,
        string $description,
        array $metadata = null,
        $adminId = null,
        $adminName = null
    ): AdminActivityLog {
        if (!$adminId) {
            $admin = Auth::guard('admin')->user();
            $adminId = $admin?->id;
            $adminName = $admin?->name ?? $admin?->email ?? 'Unknown';
        }

        $request = request();

        return AdminActivityLog::create([
            'admin_id' => $adminId ?? 0,
            'admin_name' => $adminName ?? 'System',
            'category' => $category,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? substr($request->userAgent(), 0, 500) : null,
            'created_at' => now(),
        ]);
    }

    public static function auth(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('authentication', $action, $description, $metadata);
    }

    public static function userManagement(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('user_management', $action, $description, $metadata);
    }

    public static function eventManagement(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('event_management', $action, $description, $metadata);
    }

    public static function qrManagement(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('qr_management', $action, $description, $metadata);
    }

    public static function shiftManagement(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('shift_management', $action, $description, $metadata);
    }

    public static function visitRequest(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('visit_requests', $action, $description, $metadata);
    }

    public static function connection(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('connections', $action, $description, $metadata);
    }

    public static function system(string $action, string $description, array $metadata = null): AdminActivityLog
    {
        return static::log('system', $action, $description, $metadata);
    }

    public static function cleanupOldLogs(int $daysToKeep = 30): int
    {
        return AdminActivityLog::where('created_at', '<', now()->subDays($daysToKeep))->delete();
    }
}
