<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramsRequest;
use App\Models\Programs;
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
}
