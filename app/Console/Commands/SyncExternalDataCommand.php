<?php

namespace App\Console\Commands;

use App\Services\DataSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sync:external-data {--type=all : Sync type (all, programs, subjects)}')]
#[Description('Synchronize programs and subjects from external API to local database')]
class SyncExternalDataCommand extends Command
{
    protected DataSyncService $syncService;

    public function __construct(DataSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle(): int
    {
        $this->info('🔄 Starting data synchronization...');
        $this->newLine();

        try {
            $result = $this->syncService->syncAll();

            if ($result['status'] === 'success') {
                $this->renderSuccessOutput($result);

                return self::SUCCESS;
            } else {
                $this->renderErrorOutput($result);

                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Sync failed with exception: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function renderSuccessOutput(array $result): void
    {
        $this->info('✅ '.$result['message']);
        $this->newLine();

        if (! empty($result['logs'])) {
            $this->info('📋 Sync Log:');
            $this->table(['Timestamp', 'Message', 'Details'], $this->formatLogsForTable($result['logs']));
        }

        $this->newLine();
        $this->info('✨ Synchronization completed successfully!');
    }

    private function renderErrorOutput(array $result): void
    {
        $this->error('❌ '.$result['message']);
        $this->newLine();

        if (! empty($result['logs'])) {
            $this->info('📋 Sync Log:');
            $this->table(['Timestamp', 'Message', 'Details'], $this->formatLogsForTable($result['logs']));
        }
    }

    private function formatLogsForTable(array $logs): array
    {
        return array_map(function (array $log) {
            return [
                $log['timestamp'],
                $log['message'],
                json_encode($log['context'] ?? [], JSON_PRETTY_PRINT),
            ];
        }, $logs);
    }
}
