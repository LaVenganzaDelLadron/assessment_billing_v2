<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentsRequest;
use App\Models\Enrollments;
use Illuminate\Http\JsonResponse;

class EnrollmentsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = Enrollments::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No enrollments found.'], 404);
        }
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
