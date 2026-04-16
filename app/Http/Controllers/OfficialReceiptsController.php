<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfficialReceiptsRequest;
use App\Models\OfficialReceipts;
use Illuminate\Http\JsonResponse;

class OfficialReceiptsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = OfficialReceipts::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No official receipts found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Official receipts retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(OfficialReceiptsRequest $request): JsonResponse
    {
        $data = OfficialReceipts::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Official receipt created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = OfficialReceipts::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Official receipt not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Official receipt retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(OfficialReceiptsRequest $request, string $id): JsonResponse
    {
        $data = OfficialReceipts::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Official receipt not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Official receipt updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = OfficialReceipts::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Official receipt not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Official receipt deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
