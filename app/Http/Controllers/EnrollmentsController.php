<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentsRequest;
use App\Models\Enrollments;
use Illuminate\Http\JsonResponse;

class EnrollmentsController extends Controller
{

    public function index(): JsonResponse
    {
        $enrollments = Enrollments::with([
            'student:id,student_no,first_name,last_name',
            'subject:id,name,subject_code',
            'academicTerm:id,school_year,semester',
        ])->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['message' => 'No enrollments found.'], 404);
        }

        $data = $enrollments->map(function (Enrollments $enrollment): array {
            return [
                'enrollment_id' => $enrollment->id,
                'student' => [
                    'id' => $enrollment->student?->id,
                    'student_no' => $enrollment->student?->student_no,
                    'name' => trim(($enrollment->student?->first_name ?? '').' '.($enrollment->student?->last_name ?? '')),
                ],
                'subject' => [
                    'id' => $enrollment->subject?->id,
                    'code' => $enrollment->subject?->subject_code,
                    'name' => $enrollment->subject?->name,
                ],
                'academic_term' => [
                    'id' => $enrollment->academicTerm?->id,
                    'school_year' => $enrollment->academicTerm?->school_year,
                    'semester' => $enrollment->academicTerm?->semester,
                ],
                'status' => $enrollment->status,
                'created_at' => $enrollment->created_at,
                'updated_at' => $enrollment->updated_at,
            ];
        });

        return response()->json([
            'data' => $data,
            'message' => 'Enrollments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }


    public function store(EnrollmentsRequest $request): JsonResponse
    {
        $data = Enrollments::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Enrollment created successfully.',
            'status' => 'success',
        ], 201);
    }


    public function show(string $id): JsonResponse
    {
        $data = Enrollments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Enrollment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(EnrollmentsRequest $request, string $id): JsonResponse
    {
        $data = Enrollments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Enrollment updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Enrollments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Enrollment deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
