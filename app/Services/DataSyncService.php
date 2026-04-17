<?php

namespace App\Services;

use App\Models\Programs;
use App\Models\Subjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataSyncService
{
    private const API_BASE_URL = 'https://registrarmodule1-production.up.railway.app/api';

    private const PROGRAMS_ENDPOINT = '/programs';

    private const SUBJECTS_ENDPOINT = '/subjects';

    private array $syncLog = [];

    /**
     * Execute full sync of programs and subjects
     */
    public function syncAll(): array
    {
        try {
            DB::beginTransaction();

            $this->syncPrograms();
            $this->syncSubjects();

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Data synchronization completed successfully',
                'logs' => $this->syncLog,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data sync failed', ['error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'message' => 'Data synchronization failed: '.$e->getMessage(),
                'logs' => $this->syncLog,
            ];
        }
    }

    /**
     * Sync programs from API to local database
     */
    private function syncPrograms(): void
    {
        try {
            $apiPrograms = $this->fetchFromApi(self::PROGRAMS_ENDPOINT);

            $apiExternalIds = [];
            foreach ($apiPrograms as $apiProgram) {
                $apiExternalIds[] = $apiProgram['id'];
                $this->syncProgramRecord($apiProgram);
            }

            // Delete programs not in API response (using external_id)
            $this->deleteLocalOnlyRecordsByExternalId(Programs::class, $apiExternalIds);

            $this->log('✓ Programs synced', ['total' => count($apiPrograms)]);
        } catch (\Exception $e) {
            $this->log('✗ Programs sync failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Sync a single program record
     */
    private function syncProgramRecord(array $apiProgram): void
    {
        $externalId = $apiProgram['id'];

        // Find by external_id (API ID)
        $program = Programs::where('external_id', $externalId)->first();

        $data = [
            'external_id' => $externalId,
            'name' => $apiProgram['name'],
            'code' => $apiProgram['code'] ?? null,
            'department' => $apiProgram['department'],
            'status' => $apiProgram['status'] ?? 'active',
        ];

        if ($program) {
            // Update if record exists and has changes
            if ($this->hasChanges($program, $data)) {
                $program->update($data);
                $this->log('Program updated', [
                    'custom_id' => $program->custom_id,
                    'external_id' => $externalId,
                    'code' => $apiProgram['code'],
                ]);
            }
        } else {
            // Generate custom ID only on insert
            $customId = CustomIdGenerator::generate('PRG', $externalId);

            // Create new program with custom ID
            Programs::create(array_merge($data, ['custom_id' => $customId]));
            $this->log('Program created', [
                'custom_id' => $customId,
                'external_id' => $externalId,
                'code' => $apiProgram['code'],
            ]);
        }
    }

    /**
     * Sync subjects from API to local database
     */
    private function syncSubjects(): void
    {
        try {
            $apiSubjects = $this->fetchFromApi(self::SUBJECTS_ENDPOINT);

            $apiExternalIds = [];
            foreach ($apiSubjects as $apiSubject) {
                $apiExternalIds[] = $apiSubject['id'];
                $this->syncSubjectRecord($apiSubject);
            }

            // Delete subjects not in API response (using external_id)
            $this->deleteLocalOnlyRecordsByExternalId(Subjects::class, $apiExternalIds);

            $this->log('✓ Subjects synced', ['total' => count($apiSubjects)]);
        } catch (\Exception $e) {
            $this->log('✗ Subjects sync failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Sync a single subject record and its relationships
     */
    private function syncSubjectRecord(array $apiSubject): void
    {
        $externalId = $apiSubject['id'];

        // Find by external_id (API ID)
        $subject = Subjects::where('external_id', $externalId)->first();

        $data = [
            'external_id' => $externalId,
            'name' => $apiSubject['subject_name'],
            'code' => $apiSubject['subject_code'],
            'subject_code' => $apiSubject['subject_code'],
            'units' => $apiSubject['units'] ?? 0,
            'type' => $apiSubject['type'] ?? null,
            'status' => $apiSubject['status'] ?? 'active',
        ];

        if ($subject) {
            // Update if record exists and has changes
            if ($this->hasChanges($subject, $data)) {
                $subject->update($data);
                $this->log('Subject updated', [
                    'custom_id' => $subject->custom_id,
                    'external_id' => $externalId,
                    'code' => $apiSubject['subject_code'],
                ]);
            }
        } else {
            // Generate custom ID only on insert
            $customId = CustomIdGenerator::generate('SUB', $externalId);

            // Create new subject with custom ID
            $subject = Subjects::create(array_merge($data, ['custom_id' => $customId]));
            $this->log('Subject created', [
                'custom_id' => $customId,
                'external_id' => $externalId,
                'code' => $apiSubject['subject_code'],
            ]);
        }

        // Sync program-subject relationships
        if (isset($apiSubject['programs']) && is_array($apiSubject['programs'])) {
            $this->syncSubjectProgramRelationships($subject, $apiSubject['programs']);
        }
    }

    /**
     * Sync many-to-many relationship between subjects and programs
     */
    private function syncSubjectProgramRelationships(Subjects $subject, array $apiPrograms): void
    {
        $pivotData = [];
        $apiProgramExternalIds = [];

        foreach ($apiPrograms as $apiProgram) {
            $programExternalId = $apiProgram['id'];
            $apiProgramExternalIds[] = $programExternalId;

            // Find program by external_id
            $program = Programs::where('external_id', $programExternalId)->first();

            if (! $program) {
                $this->log('Warning: Program not found for relationship', ['external_id' => $programExternalId]);

                continue;
            }

            // Prepare pivot data with extra columns
            $pivot = [
                'year_level' => $apiProgram['pivot']['year_level'] ?? null,
                'semester' => $apiProgram['pivot']['semester'] ?? null,
                'school_year' => $apiProgram['pivot']['school_year'] ?? null,
                'status' => $apiProgram['pivot']['status'] ?? 'active',
            ];

            $pivotData[$program->id] = $pivot;
        }

        // Get existing relationships
        $existingRelations = $subject->programs()
            ->pluck('program_subject.program_id')
            ->toArray();

        // Insert new relationships
        foreach ($pivotData as $programId => $pivot) {
            if (in_array($programId, $existingRelations)) {
                // Update existing relationship
                $subject->programs()->updateExistingPivot($programId, $pivot);
            } else {
                // Create new relationship
                $subject->programs()->attach($programId, $pivot);
            }
        }

        // Delete relationships not in API response
        $toDelete = array_diff($existingRelations, array_keys($pivotData));
        if (! empty($toDelete)) {
            $subject->programs()->detach($toDelete);
        }

        $this->log('Subject programs synced', [
            'subject_id' => $subject->id,
            'programs_count' => count($pivotData),
        ]);
    }

    /**
     * Fetch data from external API
     */
    private function fetchFromApi(string $endpoint): array
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->get(self::API_BASE_URL.$endpoint);

            if (! $response->successful()) {
                throw new \Exception("API returned status {$response->status()}");
            }

            return $response->json();
        } catch (\Exception $e) {
            $this->log('API fetch failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete local records that are not in API response (source of truth)
     * Uses external_id for matching
     */
    private function deleteLocalOnlyRecordsByExternalId(string $modelClass, array $apiExternalIds): void
    {
        $localOnlyRecords = $modelClass::whereNotIn('external_id', $apiExternalIds)
            ->get();

        if ($localOnlyRecords->isNotEmpty()) {
            $deletedIds = $localOnlyRecords->pluck('id')->toArray();
            $deletedExternalIds = $localOnlyRecords->pluck('external_id')->toArray();

            $modelClass::destroy($deletedIds);
            $this->log('Records deleted', [
                'model' => class_basename($modelClass),
                'count' => count($deletedIds),
                'ids' => $deletedIds,
                'external_ids' => $deletedExternalIds,
            ]);
        }
    }

    /**
     * Check if model has changes compared to new data
     */
    private function hasChanges(object $model, array $newData): bool
    {
        foreach ($newData as $key => $value) {
            if ($model->{$key} != $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log sync activity
     */
    private function log(string $message, array $context = []): void
    {
        $logEntry = [
            'timestamp' => now()->toIso8601String(),
            'message' => $message,
            'context' => $context,
        ];

        $this->syncLog[] = $logEntry;
        Log::info($message, $context);
    }

    /**
     * Get sync logs
     */
    public function getSyncLogs(): array
    {
        return $this->syncLog;
    }
}
