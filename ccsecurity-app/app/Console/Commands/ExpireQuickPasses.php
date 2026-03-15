<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuickPass;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpireQuickPasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickpass:expire-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all active quick passes that have passed their expiration time as expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-expiration of old quick passes...');
        
        try {
            // Find quick passes that are still 'active' but:
            // 1. Their specific expiration time has passed
            // 2. OR their valid_date is yesterday or earlier
            $expiredCount = QuickPass::where('status', QuickPass::STATUS_ACTIVE)
                ->where(function($q) {
                    $q->where('expires_at', '<', Carbon::now())
                      ->orWhere('valid_date', '<', Carbon::today()->toDateString());
                })
                ->update(['status' => QuickPass::STATUS_EXPIRED]);

            if ($expiredCount === 0) {
                $this->info('No old active quick passes found to expire.');
            } else {
                $message = "Successfully expired {$expiredCount} old active quick passes.";
                $this->info($message);
                Log::info($message);
            }
            
            return 0;
        } catch (\Exception $e) {
            $errorMessage = "Error expiring quick passes: " . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage);
            return 1;
        }
    }
}
