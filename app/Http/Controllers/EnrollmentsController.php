<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentRequest;
use App\Http\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentsController extends Controller
{

    public function index()
    {
        $data = Enrollment::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No enrollments found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Enrollments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(EnrollmentRequest $request)
    {
        $data = Enrollment::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Enrollment created successfully.',
            'status' => 'success',
        ], 201);
    }


    public function show(string $id)
    {
        $data = Enrollment::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }
        return response()->json([
            'data' => $enrollment,
            'message' => 'Enrollment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(EnrollmentRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = Enrollment::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Enrollment not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Enrollment updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = Enrollment::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Enrollment not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully',
        ], 200);
    }
}
