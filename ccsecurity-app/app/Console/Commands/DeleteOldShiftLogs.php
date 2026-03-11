<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShiftLog;
use App\Models\CleanupTableSetting;
use App\Models\CleanupSetting;
use Carbon\Carbon;

class DeleteOldShiftLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shiftlogs:cleanup-old {--days= : Number of days to keep shift logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old shift log records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check global auto-delete switch first
        if (!CleanupSetting::isAutoDeleteEnabled()) {
            $this->info('Global auto-delete is disabled. Skipping cleanup.');
            return 0;
        }

        // Check if auto-delete is enabled for shift_logs
        if (!CleanupTableSetting::isAutoDeleteEnabled('shift_logs')) {
            $this->info('Auto-delete is disabled for shift_logs. Skipping cleanup.');
            return 0;
        }

        $days = $this->option('days');
        
        // Use retention days from settings if not specified
        if ($days === null) {
            $days = CleanupTableSetting::getRetentionDays('shift_logs');
        }
        
        $cutoffDate = Carbon::now()->subDays($days);

        // Get count before deleting
        $deletedCount = ShiftLog::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
        // Permanently delete old shift logs
        ShiftLog::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();

        // Update last cleanup date
        $settings = CleanupTableSetting::getForTable('shift_logs');
        $settings->updateLastCleanupDate();

        $this->info("Cleanup completed! {$deletedCount} shift log records deleted.");

        return 0;
    }
}
