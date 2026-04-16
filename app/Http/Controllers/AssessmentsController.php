<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentsRequest;
use App\Models\Assessments;
use Illuminate\Http\JsonResponse;

class AssessmentsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = Assessments::all();
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
            'data' => $data,
            'message' => 'Assessment created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Assessments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }
        return response()->json([
            'data' => $data,
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
            'data' => $data->fresh(),
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
}
