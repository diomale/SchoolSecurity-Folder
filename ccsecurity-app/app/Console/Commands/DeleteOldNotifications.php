<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EntryLog;
use Carbon\Carbon;

class DeleteOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup-old {--days=30 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old notification/entry log records (no logging)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        // Permanently delete old entry logs (which serve as notifications for guards)
        EntryLog::where('scan_at', '<', $cutoffDate->toDateTimeString())->delete();

        return 0;
    }
}
