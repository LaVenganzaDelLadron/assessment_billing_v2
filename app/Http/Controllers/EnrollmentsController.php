<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentsRequest;
use App\Models\Enrollments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $grouped = $enrollments->groupBy('student_id')->map(function ($items) {

            $student = $items->first()->student;

            return [
                'student' => [
                    'id' => $student?->id,
                    'student_no' => $student?->student_no,
                    'name' => trim(($student?->first_name ?? '').' '.($student?->last_name ?? '')),
                ],
                'academic_term' => [
                    'id' => $items->first()->academicTerm?->id,
                    'school_year' => $items->first()->academicTerm?->school_year,
                    'semester' => $items->first()->academicTerm?->semester,
                ],
                'subjects' => $items->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->subject?->id,
                        'code' => $enrollment->subject?->subject_code,
                        'name' => $enrollment->subject?->name,
                        'status' => $enrollment->status,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'data' => $grouped,
            'message' => 'Enrollments grouped successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentIndex(Request $request): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $enrollments = Enrollments::query()
            ->with([
                'student:id,student_no,first_name,last_name',
                'subject:id,name,subject_code',
                'academicTerm:id,school_year,semester',
            ])
            ->where('student_id', $student->id)
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['message' => 'No enrollments found.'], 404);
        }

        $grouped = $enrollments->groupBy('student_id')->map(function ($items) {
            $student = $items->first()->student;

            return [
                'student' => [
                    'id' => $student?->id,
                    'student_no' => $student?->student_no,
                    'name' => trim(($student?->first_name ?? '').' '.($student?->last_name ?? '')),
                ],
                'academic_term' => [
                    'id' => $items->first()->academicTerm?->id,
                    'school_year' => $items->first()->academicTerm?->school_year,
                    'semester' => $items->first()->academicTerm?->semester,
                ],
                'subjects' => $items->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->subject?->id,
                        'code' => $enrollment->subject?->subject_code,
                        'name' => $enrollment->subject?->name,
                        'status' => $enrollment->status,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'data' => $grouped,
            'message' => 'Enrollments grouped successfully.',
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

    public function studentShow(Request $request, string $id): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = Enrollments::query()
            ->with([
                'student:id,student_no,first_name,last_name',
                'subject:id,name,subject_code',
                'academicTerm:id,school_year,semester',
            ])
            ->where('student_id', $student->id)
            ->find($id);

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
