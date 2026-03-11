<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shift;
use App\Models\CleanupTableSetting;
use App\Models\CleanupSetting;
use Carbon\Carbon;

class DeleteOldShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:cleanup-old {--days= : Number of days to keep shift assignments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old shift assignment records';

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

        // Check if auto-delete is enabled for shifts
        if (!CleanupTableSetting::isAutoDeleteEnabled('shifts')) {
            $this->info('Auto-delete is disabled for shifts. Skipping cleanup.');
            return 0;
        }

        $days = $this->option('days');
        
        // Use retention days from settings if not specified
        if ($days === null) {
            $days = CleanupTableSetting::getRetentionDays('shifts');
        }
        
        $cutoffDate = Carbon::now()->subDays($days);

        // Get count before deleting
        $deletedCount = Shift::where('shift_date', '<=', $cutoffDate->toDateString())->count();
        // Permanently delete old shifts
        Shift::where('shift_date', '<=', $cutoffDate->toDateString())->delete();

        // Update last cleanup date
        $settings = CleanupTableSetting::getForTable('shifts');
        $settings->updateLastCleanupDate();

        $this->info("Cleanup completed! {$deletedCount} shift assignment records deleted.");

        return 0;
    }
}
