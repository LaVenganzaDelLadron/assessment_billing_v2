<?php

namespace App\Http\Controllers;


use App\Http\Requests\AssessmentRequest;
use App\Http\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentsController extends Controller
{

    public function index()
    {
        $data = Assessment::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No assessments found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Assessments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AssessmentRequest $request)
    {
        $data = Assessment::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Assessment created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $assessment = Assessment::query()->find($id);
        if ($assessment === null) {
            return response()->json(['message' => 'Assessment not found.'], 404);
        }
        return response()->json([
            'data' => $assessment,
            'message' => 'Assessment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AssessmentRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = Assessment::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Assessment not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Assessment updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = Assessment::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Assessment not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Assessment deleted successfully',
        ], 200);
    }
}
