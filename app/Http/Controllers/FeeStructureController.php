<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeeStructureRequest;
use App\Http\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $data = FeeStructure::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No fee structures found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Fee structures retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(FeeStructureRequest $request)
    {
        $data = FeeStructure::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Fee structure created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $feeStructure = FeeStructure::query()->find($id);
        if ($feeStructure === null) {
            return response()->json(['message' => 'Fee structure not found.'], 404);
        }
        return response()->json([
            'data' => $feeStructure,
            'message' => 'Fee structure retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(FeeStructureRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = FeeStructure::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Fee structure not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Fee structure updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = FeeStructure::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Fee structure not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Fee structure deleted successfully',
        ], 200);
    }
}
