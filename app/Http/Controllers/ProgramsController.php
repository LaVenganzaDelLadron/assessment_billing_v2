<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramsRequest;
use App\Models\Programs;
use App\Services\DataSyncService;
use Illuminate\Http\JsonResponse;

class ProgramsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Programs::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No programs found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Programs retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function publicIndex(DataSyncService $syncService): JsonResponse
    {
        $syncFailureResponse = $this->syncPrograms($syncService);

        if ($syncFailureResponse !== null) {
            return $syncFailureResponse;
        }

        return response()->json(
            Programs::query()
                ->whereNotNull('external_id')
                ->orderBy('external_id')
                ->get()
                ->map(fn (Programs $program): array => $this->formatPublicProgram($program))
                ->values(),
            200
        );
    }

    public function store(ProgramsRequest $request): JsonResponse
    {
        $data = Programs::create($request->validated());

        return response()->json([
            'data' => $data,
            'message' => 'Program created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Programs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Program not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Program retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(ProgramsRequest $request, string $id): JsonResponse
    {
        $data = Programs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Program not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Program updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Programs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Program not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Program deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    private function syncPrograms(DataSyncService $syncService): ?JsonResponse
    {
        $result = $syncService->syncProgramsOnly();

        if ($result['status'] === 'success') {
            return null;
        }

        if (Programs::query()->whereNotNull('external_id')->exists()) {
            return null;
        }

        return response()->json([
            'message' => $result['message'],
            'status' => 'error',
        ], 502);
    }

    /**
     * @return array{id:int|null, code:?string, name:string, department:?string, status:?string, created_at:?string, updated_at:?string}
     */
    private function formatPublicProgram(Programs $program): array
    {
        return [
            'id' => $program->external_id ?? $program->id,
            'code' => $program->code,
            'name' => $program->name,
            'department' => $program->department,
            'status' => $program->status,
            'created_at' => $program->created_at?->toJSON(),
            'updated_at' => $program->updated_at?->toJSON(),
        ];
    }
}
