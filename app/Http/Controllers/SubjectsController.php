<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectsRequest;
use App\Models\Programs;
use App\Models\Subjects;
use App\Services\DataSyncService;
use Illuminate\Http\JsonResponse;

class SubjectsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Subjects::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No subjects found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Subjects retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function publicIndex(DataSyncService $syncService): JsonResponse
    {
        $syncFailureResponse = $this->syncSubjects($syncService);

        if ($syncFailureResponse !== null) {
            return $syncFailureResponse;
        }

        return response()->json(
            Subjects::query()
                ->whereNotNull('external_id')
                ->with(['programs' => fn ($query) => $query->whereNotNull('external_id')->orderBy('external_id')])
                ->orderBy('external_id')
                ->get()
                ->map(fn (Subjects $subject): array => $this->formatPublicSubject($subject))
                ->values(),
            200
        );
    }

    public function store(SubjectsRequest $request): JsonResponse
    {
        $data = Subjects::create($request->validated());

        return response()->json([
            'data' => $data,
            'message' => 'Subject created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Subjects::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Subject retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(SubjectsRequest $request, string $id): JsonResponse
    {
        $data = Subjects::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Subject updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Subjects::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Subject deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    private function syncSubjects(DataSyncService $syncService): ?JsonResponse
    {
        $result = $syncService->syncAll();

        if ($result['status'] === 'success') {
            return null;
        }

        $hasPrograms = Programs::query()->whereNotNull('external_id')->exists();
        $hasSubjects = Subjects::query()->whereNotNull('external_id')->exists();

        if ($hasPrograms && $hasSubjects) {
            return null;
        }

        return response()->json([
            'message' => $result['message'],
            'status' => 'error',
        ], 502);
    }

    /**
     * @return array{id:int|null, subject_code:?string, subject_name:string, units:float, type:?string, status:?string, created_at:?string, updated_at:?string, programs:array<int, array{id:int|null, code:?string, name:string, department:?string, status:?string, created_at:?string, updated_at:?string, pivot:array{year_level:?string, semester:?string, school_year:?string, status:?string}}>}
     */
    private function formatPublicSubject(Subjects $subject): array
    {
        return [
            'id' => $subject->external_id ?? $subject->id,
            'subject_code' => $subject->subject_code ?? $subject->code,
            'subject_name' => $subject->name,
            'units' => (float) $subject->units,
            'type' => $subject->type,
            'status' => $subject->status,
            'created_at' => $subject->created_at?->toJSON(),
            'updated_at' => $subject->updated_at?->toJSON(),
            'programs' => $subject->programs
                ->map(fn (Programs $program): array => $this->formatPublicProgram($program))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{id:int|null, code:?string, name:string, department:?string, status:?string, created_at:?string, updated_at:?string, pivot:array{year_level:?string, semester:?string, school_year:?string, status:?string}}
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
            'pivot' => [
                'year_level' => $program->pivot->year_level,
                'semester' => $program->pivot->semester,
                'school_year' => $program->pivot->school_year,
                'status' => $program->pivot->status,
            ],
        ];
    }
}
