<?php

namespace App\Console;

use App\Http\Middleware\AuthMiddleware;
use App\Jobs\SyncExternalDataJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $routeMiddleware = [
        'custom.auth' => AuthMiddleware::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Keep the local database close to the external source of truth.
        $schedule->job(new SyncExternalDataJob)
            ->everyFiveMinutes()
            ->name('sync-external-data')
            ->withoutOverlapping(10)
            ->onFailure(function () {
                Log::error('Scheduled sync failed');
            });

        // Optional: Run at specific time (e.g., 2 AM daily)
        // $schedule->job(new SyncExternalDataJob())
        //     ->dailyAt('02:00')
        //     ->name('sync-external-data-daily');
    }
}
