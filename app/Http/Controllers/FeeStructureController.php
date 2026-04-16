<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeeStructureRequest;
use App\Models\FeeStructure;
use Illuminate\Http\JsonResponse;

class FeeStructureController extends Controller
{
    public function index(): JsonResponse
    {
        $data = FeeStructure::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No fee structures found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Fee structures retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(FeeStructureRequest $request): JsonResponse
    {
        $data = FeeStructure::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Fee structure created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = FeeStructure::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Fee structure not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Fee structure retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(FeeStructureRequest $request, string $id): JsonResponse
    {
        $data = FeeStructure::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Fee structure not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Fee structure updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = FeeStructure::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Fee structure not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Fee structure deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
