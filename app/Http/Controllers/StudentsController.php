<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentsRequest;
use App\Models\Students;
use Illuminate\Http\JsonResponse;

class StudentsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = Students::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No students found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Students retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(StudentsRequest $request): JsonResponse
    {
        $data = Students::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Student created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Students::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Student retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(StudentsRequest $request, string $id): JsonResponse
    {
        $data = Students::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Student updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Students::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Student deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
