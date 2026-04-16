<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentBreakdownRequest;
use App\Models\AssessmentBreakdown;
use Illuminate\Http\JsonResponse;

class AssessmentBreakdownController extends Controller
{

    public function index(): JsonResponse
    {
        $data = AssessmentBreakdown::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No assessment breakdowns found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Assessment breakdowns retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AssessmentBreakdownRequest $request): JsonResponse
    {
        $data = AssessmentBreakdown::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Assessment breakdown created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = AssessmentBreakdown::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Assessment breakdown not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Assessment breakdown retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AssessmentBreakdownRequest $request, string $id): JsonResponse
    {
        $data = AssessmentBreakdown::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Assessment breakdown not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Assessment breakdown updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = AssessmentBreakdown::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Assessment breakdown not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Assessment breakdown deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
