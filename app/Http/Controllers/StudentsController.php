<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = Student::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No students found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Students retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(StudentRequest $request): JsonResponse
    {
        $data = Student::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Student created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Student::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Student retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(StudentRequest $request, string $id): JsonResponse
    {
        $data = Student::query()->find($id);
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
        $data = Student::query()->find($id);
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
