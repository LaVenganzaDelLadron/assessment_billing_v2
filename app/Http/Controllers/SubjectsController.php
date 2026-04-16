<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Http\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{

    public function index()
    {
        $data = Subject::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No subjects found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Subjects retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(SubjectRequest $request)
    {
        $data = Subject::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Subject created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = Subject::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Subject retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(SubjectRequest $request, string $id)
    {
        $data = Subject::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Subject updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id)
    {
        $data = Subject::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Subject deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
