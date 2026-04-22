<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentsRequest;
use App\Models\Assessments;
use Illuminate\Http\JsonResponse;

class AssessmentsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Assessments::query()
            ->with([
                'student:id,student_no,first_name,last_name',
                'academicTerm:id,school_year,semester',
            ])
            ->latest()
            ->get()
            ->map(fn (Assessments $assessment): array => $this->transformAssessment($assessment))
            ->values();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No assessments found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Assessments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AssessmentsRequest $request): JsonResponse
    {
        $data = Assessments::create($request->validated());

        return response()->json([
            'data' => $this->transformAssessment($data->load([
                'student:id,student_no,first_name,last_name',
                'academicTerm:id,school_year,semester',
            ])),
            'message' => 'Assessment created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Assessments::query()
            ->with([
                'student:id,student_no,first_name,last_name',
                'academicTerm:id,school_year,semester',
            ])
            ->find($id);

        if ($data === null) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }

        return response()->json([
            'data' => $this->transformAssessment($data),
            'message' => 'Assessment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AssessmentsRequest $request, string $id): JsonResponse
    {
        $data = Assessments::find($id);

        if ($data === null) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }

        $data->update($request->validated());

        return response()->json([
            'data' => $this->transformAssessment($data->fresh()->load([
                'student:id,student_no,first_name,last_name',
                'academicTerm:id,school_year,semester',
            ])),
            'message' => 'Assessment updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Assessments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Assessment deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    /**
     * @return array{
     *     id: mixed,
     *     student_id: mixed,
     *     academic_term_id: mixed,
     *     total_units: mixed,
     *     status: mixed,
     *     created_at: mixed,
     *     updated_at: mixed,
     *     student: array{id: mixed, student_no: mixed, name: string},
     *     academic_term: array{id: mixed, school_year: mixed, semester: mixed}
     * }
     */
    private function transformAssessment(Assessments $assessment): array
    {
        return [
            'id' => $assessment->id,
            'student_id' => $assessment->student_id,
            'academic_term_id' => $assessment->academic_term_id,
            'total_units' => $assessment->total_units,
            'status' => $assessment->status,
            'created_at' => $assessment->created_at,
            'updated_at' => $assessment->updated_at,
            'student' => [
                'id' => $assessment->student?->id,
                'student_no' => $assessment->student?->student_no,
                'name' => trim(($assessment->student?->first_name ?? '').' '.($assessment->student?->last_name ?? '')),
            ],
            'academic_term' => [
                'id' => $assessment->academicTerm?->id,
                'school_year' => $assessment->academicTerm?->school_year,
                'semester' => $assessment->academicTerm?->semester,
            ],
        ];
    }
}
