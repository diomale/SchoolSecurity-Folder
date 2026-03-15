<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EntryLog;
use App\Models\VisitRequest;
use App\Models\Notification;
use App\Models\ShiftLog;
use App\Models\Shift;
use App\Models\QuickPass;
use App\Models\CleanupTableSetting;
use App\Models\CleanupSetting;
use Carbon\Carbon;

class RunAllCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:run-all {--days= : Override retention days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run cleanup for all tables (entry logs, visit requests, notifications, shift logs, shifts). Does NOT clear sessions or cache.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup process...');
        $this->newLine();

        // Check global auto-delete switch
        if (!CleanupSetting::isAutoDeleteEnabled()) {
            $this->error('Global auto-delete is DISABLED. Skipping all cleanup.');
            $this->info('Enable it in the cleanup_settings table or from the admin panel.');
            return 0;
        }

        $daysOverride = $this->option('days');

        // Cleanup Entry Logs
        $this->cleanupTable('entry_logs', 'Entry Logs', function($cutoffDate) {
            return EntryLog::whereRaw('STR_TO_DATE(scan_at, "%Y-%m-%d %H:%i:%s") <= ?', [$cutoffDate->toDateTimeString()])->count();
        }, function($cutoffDate) {
            return EntryLog::whereRaw('STR_TO_DATE(scan_at, "%Y-%m-%d %H:%i:%s") <= ?', [$cutoffDate->toDateTimeString()])->delete();
        }, $daysOverride);

        // Cleanup Visit Requests
        $this->cleanupTable('visit_requests', 'Visit Requests', function($cutoffDate) {
            return VisitRequest::where('created_at', '<=', $cutoffDate)->count();
        }, function($cutoffDate) {
            return VisitRequest::where('created_at', '<=', $cutoffDate)->delete();
        }, $daysOverride);

        // Cleanup Notifications
        $this->cleanupTable('notifications', 'Notifications', function($cutoffDate) {
            return Notification::where('created_at', '<=', $cutoffDate)->count();
        }, function($cutoffDate) {
            return Notification::where('created_at', '<=', $cutoffDate)->delete();
        }, $daysOverride);

        // Cleanup Shift Logs
        $this->cleanupTable('shift_logs', 'Shift Logs', function($cutoffDate) {
            return ShiftLog::where('created_at', '<=', $cutoffDate)->count();
        }, function($cutoffDate) {
            return ShiftLog::where('created_at', '<=', $cutoffDate)->delete();
        }, $daysOverride);

        // Cleanup Old Shifts
        $this->cleanupTable('shifts', 'Shift Assignments', function($cutoffDate) {
            return Shift::where('shift_date', '<', $cutoffDate->format('Y-m-d'))->count();
        }, function($cutoffDate) {
            return Shift::where('shift_date', '<', $cutoffDate->format('Y-m-d'))->delete();
        }, $daysOverride);

        // Cleanup Old Quick Passes
        $this->cleanupTable('quick_passes', 'Quick Passes', function($cutoffDate) {
            return QuickPass::where('created_at', '<=', $cutoffDate)->count();
        }, function($cutoffDate) {
            return QuickPass::where('created_at', '<=', $cutoffDate)->delete();
        }, $daysOverride);

        $this->newLine();
        $this->info('✅ All cleanup tasks completed! Sessions and cache were NOT affected.');

        return 0;
    }

    /**
     * Clean up a specific table
     */
    private function cleanupTable($tableName, $label, $countCallback, $deleteCallback, $daysOverride = null)
    {
        $this->info("Cleaning up: {$label}");

        // Check if auto-delete is enabled for this table
        if (!CleanupTableSetting::isAutoDeleteEnabled($tableName)) {
            $this->warn("  ⚠ Auto-delete is DISABLED for {$label}. Skipping...");
            $this->newLine();
            return;
        }

        // Get retention days
        $days = $daysOverride ?? CleanupTableSetting::getRetentionDays($tableName);
        $cutoffDate = Carbon::now()->subDays($days);

        // Get count before deleting
        $deletedCount = $countCallback($cutoffDate);

        if ($deletedCount === 0) {
            $this->info("  ✓ No records older than {$days} days. Nothing to delete.");
        } else {
            // Perform deletion
            $deleteCallback($cutoffDate);

            // Update last cleanup date
            $settings = CleanupTableSetting::getForTable($tableName);
            $settings->updateLastCleanupDate();

            $this->info("  ✓ Deleted {$deletedCount} record(s) older than {$days} days.");
        }

        $this->newLine();
    }
}
