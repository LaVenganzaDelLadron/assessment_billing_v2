<?php

namespace App\Http\Controllers;

use App\Services\DataSyncService;
use Illuminate\Http\JsonResponse;

class DataSyncController extends Controller
{
    public function __construct(private DataSyncService $syncService) {}

    /**
     * Manually trigger data synchronization
     */
    public function sync(): JsonResponse
    {
        try {
            $result = $this->syncService->syncAll();

            if ($result['status'] === 'success') {
                return response()->json([
                    'data' => $result,
                    'message' => 'Data synchronization completed successfully',
                    'status' => 'success',
                ], 200);
            } else {
                return response()->json([
                    'data' => $result,
                    'message' => 'Data synchronization encountered errors',
                    'status' => 'error',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data synchronization failed: '.$e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Get sync logs and status
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'data' => [
                'last_sync' => cache('sync_last_run'),
                'sync_running' => cache('sync_running', false),
                'logs' => $this->syncService->getSyncLogs(),
            ],
            'message' => 'Sync status retrieved',
            'status' => 'success',
        ], 200);
    }
}
