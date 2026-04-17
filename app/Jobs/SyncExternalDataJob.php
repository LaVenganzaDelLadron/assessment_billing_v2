<?php

namespace App\Jobs;

use App\Services\DataSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncExternalDataJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 3;

    /**
     * Execute the job.
     */
    public function handle(DataSyncService $syncService): void
    {
        try {
            // Prevent concurrent syncs
            if (Cache::get('sync_running', false)) {
                Log::warning('Sync already in progress, skipping this job');

                return;
            }

            Cache::put('sync_running', true, now()->addMinutes(5));

            Log::info('Starting scheduled data synchronization');

            $result = $syncService->syncAll();

            Cache::put('sync_last_run', [
                'timestamp' => now()->toIso8601String(),
                'status' => $result['status'],
                'message' => $result['message'],
            ]);

            Log::info('Data synchronization completed', $result);
        } catch (\Exception $e) {
            Log::error('Scheduled sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        } finally {
            Cache::forget('sync_running');
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Sync job failed permanently', [
            'error' => $exception->getMessage(),
        ]);
        Cache::forget('sync_running');
    }
}
