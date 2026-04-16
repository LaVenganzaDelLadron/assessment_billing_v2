<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicTermsRequest;
use App\Models\AcademicTerms;
use Illuminate\Http\JsonResponse;


class AcademicTermsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = AcademicTerms::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No academic terms found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Academic terms retrieved successfully.',
            'status' => 'success',
        ], 200);
    }


    public function store(AcademicTermsRequest $request): JsonResponse
    {
        $data = AcademicTerms::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Academic term created successfully.',
            'status' => 'success',
        ], 201);
    }


    public function show(string $id): JsonResponse
    {
        $data = AcademicTerms::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Academic term not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Academic term retrieved successfully.',
            'status' => 'success',
        ], 200);
    }


    public function update(AcademicTermsRequest $request, string $id): JsonResponse
    {
        $data = AcademicTerms::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Academic term not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Academic term updated successfully.',
            'status' => 'success',
        ], 200);
    }


    public function destroy(string $id): JsonResponse
    {
        $data = AcademicTerms::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Academic term not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Academic term deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
