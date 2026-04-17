<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeachersRequest;
use App\Models\Teachers;
use Illuminate\Http\JsonResponse;

class TeachersController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Teachers::with('user')->get();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No teachers found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Teachers retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(TeachersRequest $request): JsonResponse
    {
        $data = Teachers::create($request->validated());

        return response()->json([
            'data' => $data->load('user'),
            'message' => 'Teacher created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Teachers::with('user')->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Teacher retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(TeachersRequest $request, string $id): JsonResponse
    {
        $data = Teachers::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh()->load('user'),
            'message' => 'Teacher updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Teachers::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
