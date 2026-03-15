<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\QuickPass;
use App\Models\CleanupTableSetting;
use App\Models\CleanupSetting;
use Carbon\Carbon;

class DeleteOldQuickPasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickpass:cleanup-old {--days= : Number of days to keep quick pass records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old quick pass records';

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

        // Check if auto-delete is enabled for quick_passes
        if (!CleanupTableSetting::isAutoDeleteEnabled('quick_passes')) {
            $this->info('Auto-delete is disabled for quick_passes. Skipping cleanup.');
            return 0;
        }

        $days = $this->option('days');

        // Use retention days from settings if not specified
        if ($days === null) {
            $days = CleanupTableSetting::getRetentionDays('quick_passes');
        }

        // If retention days is 0, delete ALL records (no retention)
        if ($days === 0 || $days === '0') {
            // Disable foreign key checks to allow deletion
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            $deletedCount = QuickPass::count();
            QuickPass::query()->delete();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Update last cleanup date
            $settings = CleanupTableSetting::getForTable('quick_passes');
            $settings->updateLastCleanupDate();

            $this->info("Cleanup completed! {$deletedCount} quick pass records deleted (all records - 0 days retention).");
            return 0;
        }

        $cutoffDate = Carbon::now()->subDays($days);

        // Get count before deleting
        $deletedCount = QuickPass::where('created_at', '<=', $cutoffDate->toDateTimeString())
            ->count();

        // Permanently delete old quick passes
        QuickPass::where('created_at', '<=', $cutoffDate->toDateTimeString())->delete();

        // Update last cleanup date
        $settings = CleanupTableSetting::getForTable('quick_passes');
        $settings->updateLastCleanupDate();

        $this->info("Cleanup completed! {$deletedCount} quick pass records deleted.");

        return 0;
    }
}
