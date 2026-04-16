<?php

namespace App\Http\Controllers;

use App\Http\Request\AcademicTermRequest;
use App\Http\Models\AcademicTerms;
use Illuminate\Http\Request;


class AcademicTermsController extends Controller
{
    public function index()
    {
        $data = AcademicTerms::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No academic terms found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Academic terms retrieved successfully.',
            'status' => 'success',
        ], 200);
    }


    public function store(StoreAcademicTermRequest $request)
    {
        $data = AcademicTerms::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Academic term created successfully.',
            'status' => 'success',
        ], 201);
    }


    public function show(string $id)
    {
        $data = AcademicTerms::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Academic term not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Academic term retrieved successfully.',
            'status' => 'success',
        ], 200);
    }


    public function update(AcademicTermRequest $request, string $id)
    {
        $data = $request->validated();
        $data = AcademicTerms::query()->find($id);

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
        $data = AcademicTerms::query()->find($id);
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
