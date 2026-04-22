<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyScholarshipRequest;
use App\Http\Requests\ScholarshipRequest;
use App\Models\Scholarship;
use App\Models\Students;
use App\Models\StudentScholarship;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class SchollarshipController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Scholarship::query()
            ->with('studentScholarships')
            ->latest()
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No scholarships found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Scholarships retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentsWithScholarships(): JsonResponse
    {
        $data = StudentScholarship::query()
            ->with(['student', 'scholarship'])
            ->latest()
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No students with scholarships found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Students with scholarships retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(ScholarshipRequest $request): JsonResponse
    {
        $data = Scholarship::create($request->validated());

        return response()->json([
            'data' => $data,
            'message' => 'Scholarship created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Scholarship::query()
            ->with('studentScholarships.student')
            ->find($id);

        if ($data === null) {
            return response()->json(['message' => 'Scholarship not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Scholarship retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(ScholarshipRequest $request, string $id): JsonResponse
    {
        $data = Scholarship::find($id);

        if ($data === null) {
            return response()->json(['message' => 'Scholarship not found.'], 404);
        }

        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Scholarship updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Scholarship::find($id);

        if ($data === null) {
            return response()->json(['message' => 'Scholarship not found.'], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Scholarship deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    public function applyScholarship(ApplyScholarshipRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $student = Students::find($validated['student_id']);
        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $scholarship = Scholarship::find($validated['scholarship_id']);
        if ($scholarship === null) {
            return response()->json(['message' => 'Scholarship not found.'], 404);
        }

        if (! $scholarship->is_active) {
            return response()->json(['message' => 'This scholarship is inactive and cannot be applied.'], 422);
        }

        [
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ] = $this->calculateScholarshipAmounts($validated, $scholarship);

        $data = StudentScholarship::create([
            'student_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'applied_at' => $validated['applied_at'] ?? now(),
        ]);

        return response()->json([
            'data' => $data->load(['student', 'scholarship']),
            'message' => 'Scholarship applied successfully.',
            'status' => 'success',
        ], 201);
    }

    public function updateAppliedScholarship(ApplyScholarshipRequest $request, string $id): JsonResponse
    {
        $data = StudentScholarship::find($id);

        if ($data === null) {
            return response()->json(['message' => 'Applied scholarship not found.'], 404);
        }

        $validated = $request->validated();

        $student = isset($validated['student_id'])
            ? Students::find($validated['student_id'])
            : $data->student;

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $scholarship = isset($validated['scholarship_id'])
            ? Scholarship::find($validated['scholarship_id'])
            : $data->scholarship;

        if ($scholarship === null) {
            return response()->json(['message' => 'Scholarship not found.'], 404);
        }

        if (! $scholarship->is_active) {
            return response()->json(['message' => 'This scholarship is inactive and cannot be applied.'], 422);
        }

        [
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ] = $this->calculateScholarshipAmounts([
            'discount_type' => $validated['discount_type'] ?? $data->discount_type,
            'discount_value' => $validated['discount_value'] ?? $data->discount_value,
            'original_amount' => $validated['original_amount'] ?? $data->original_amount,
        ], $scholarship);

        $data->update([
            'student_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'applied_at' => $validated['applied_at'] ?? $data->applied_at,
        ]);

        return response()->json([
            'data' => $data->fresh()->load(['student', 'scholarship']),
            'message' => 'Applied scholarship updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroyAppliedScholarship(string $id): JsonResponse
    {
        $data = StudentScholarship::find($id);

        if ($data === null) {
            return response()->json(['message' => 'Applied scholarship not found.'], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Applied scholarship deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    /**
     * @return array{
     *     discount_type: string,
     *     discount_value: float,
     *     original_amount: float,
     *     discount_amount: float,
     *     final_amount: float
     * }
     */
    private function calculateScholarshipAmounts(array $validated, Scholarship $scholarship): array
    {
        $discountType = $validated['discount_type'] ?? $scholarship->discount_type;
        $discountValue = (float) ($validated['discount_value'] ?? $scholarship->discount_value);
        $originalAmount = (float) $validated['original_amount'];

        if ($discountType === 'percent' && $discountValue > 100) {
            throw new HttpResponseException(
                response()->json(['message' => 'Percent discount may not be greater than 100.'], 422)
            );
        }

        $discountAmount = $discountType === 'percent'
            ? round($originalAmount * ($discountValue / 100), 2)
            : round($discountValue, 2);

        $discountAmount = min($discountAmount, $originalAmount);

        return [
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => round($originalAmount - $discountAmount, 2),
        ];
    }
}
