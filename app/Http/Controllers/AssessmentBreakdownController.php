<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentBreakdownRequest;
use App\Http\Models\AssessmentBreakdown;
use Illuminate\Http\Request;

class AssessmentBreakdownController extends Controller
{

    public function index()
    {
        $data = AssessmentBreakdown::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No assessment breakdowns found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Assessment breakdowns retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AssessmentBreakdownRequest $request)
    {
        $assessmentBreakdown = AssessmentBreakdown::query()->create($request->validated());
        return response()->json([
            'data' => $assessmentBreakdown,
            'message' => 'Assessment breakdown created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $assessmentBreakdown = AssessmentBreakdown::query()->find($id);
        if ($assessmentBreakdown === null) {
            return response()->json(['message' => 'Assessment breakdown not found.'], 404);
        }
        return response()->json([
            'data' => $assessmentBreakdown,
            'message' => 'Assessment breakdown retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AssessmentBreakdownRequest $request, string $id)
    {
        $data = $request->validated();
        $data = AssessmentBreakdown::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'data not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = AssessmentBreakdown::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'data not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'deleted successfully',
        ], 200);
    }
}
