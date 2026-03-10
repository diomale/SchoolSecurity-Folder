<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitRequest;
use App\Models\Notification;
use App\Models\CleanupTableSetting;
use App\Models\CleanupSetting;
use Carbon\Carbon;

class DeleteOldVisitRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitrequests:cleanup-old {--days= : Number of days to keep visit requests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old visit request and notification records';

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

        $days = $this->option('days');
        $totalDeleted = 0;

        // Cleanup visit_requests
        if (CleanupTableSetting::isAutoDeleteEnabled('visit_requests')) {
            $visitDays = $days !== null ? $days : CleanupTableSetting::getRetentionDays('visit_requests');
            $cutoffDate = Carbon::now()->subDays($visitDays);

            $deletedCount = VisitRequest::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            VisitRequest::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();

            $totalDeleted += $deletedCount;

            // Update last cleanup date
            $settings = CleanupTableSetting::getForTable('visit_requests');
            $settings->updateLastCleanupDate();

            $this->info("Deleted {$deletedCount} visit requests.");
        } else {
            $this->info('Auto-delete is disabled for visit_requests. Skipping.');
        }

        // Cleanup notifications
        if (CleanupTableSetting::isAutoDeleteEnabled('notifications')) {
            $notifDays = $days !== null ? $days : CleanupTableSetting::getRetentionDays('notifications');
            $cutoffDate = Carbon::now()->subDays($notifDays);

            $deletedCount = Notification::where('created_at', '<=', $cutoffDate->toDateTimeString())->count();
            Notification::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();

            $totalDeleted += $deletedCount;

            // Update last cleanup date
            $settings = CleanupTableSetting::getForTable('notifications');
            $settings->updateLastCleanupDate();

            $this->info("Deleted {$deletedCount} notifications.");
        } else {
            $this->info('Auto-delete is disabled for notifications. Skipping.');
        }

        $this->info("Total cleanup completed! {$totalDeleted} records deleted.");

        return 0;
    }
}
