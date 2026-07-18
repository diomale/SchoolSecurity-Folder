<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AdminActivityLog;
use App\Services\AdminActivityLogger;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Search in description and action
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('admin_name', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // Stats for the header
        $todayCount = AdminActivityLog::whereDate('created_at', today())->count();
        $weekCount = AdminActivityLog::where('created_at', '>=', now()->subWeek())->count();
        $totalAdmins = AdminActivityLog::distinct('admin_id')->count('admin_id');

        // Get all admins who have activity for the filter dropdown
        $admins = AdminActivityLog::select('admin_id', 'admin_name')
            ->distinct()
            ->orderBy('admin_name')
            ->get();

        // Category counts for the sidebar
        $categoryCounts = AdminActivityLog::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('Admin.ActivityLogs.index', compact(
            'logs', 'todayCount', 'weekCount', 'totalAdmins', 'admins', 'categoryCounts'
        ));
    }

    public function clearOld()
    {
        $deleted = AdminActivityLogger::cleanupOldLogs(30);

        AdminActivityLogger::system('clear_logs', "Cleared {$deleted} activity logs older than 30 days.");

        return redirect()->back()->with('success', "Cleared {$deleted} old activity log entries.");
    }
}
