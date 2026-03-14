<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutsideUser;
use Illuminate\Support\Facades\Log;

class DeactivateOutsiderQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:deactivate-outsiders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate all active outsider QR codes at the end of the day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting deactivation of active outsider QR codes...');
        
        try {
            // Count active ones before deactivating
            $activeCount = OutsideUser::where('qr_status', 'active')->count();
            
            if ($activeCount === 0) {
                $this->info('No active outsider QR codes found to deactivate.');
                return 0;
            }

            // Perform deactivation
            $deactivatedCount = OutsideUser::where('qr_status', 'active')
                ->update(['qr_status' => 'inactive']);

            $message = "Successfully deactivated {$deactivatedCount} active outsider QR codes.";
            $this->info($message);
            Log::info($message);
            
            return 0;
        } catch (\Exception $e) {
            $errorMessage = "Error deactivating outsider QR codes: " . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage);
            return 1;
        }
    }
}
