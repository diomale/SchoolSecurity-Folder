<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitRequest;
use App\Models\Notification;
use Carbon\Carbon;

class DeleteOldVisitRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitrequests:cleanup-old {--days=30 : Number of days to keep visit requests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete old visit request records older than specified days (default: 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        // Permanently delete old visit requests
        VisitRequest::where('created_at', '<', $cutoffDate->toDateTimeString())->delete();

                // Permanently delete old notifications (outside user notifications)
        $notificationsDeleted = Notification::where('created_at', '<', $cutoffDate->toDateTimeString())->delete();

        return 0;
    }
}
