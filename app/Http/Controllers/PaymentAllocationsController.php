<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentAllocationsRequest;
use App\Models\PaymentAllocations;
use Illuminate\Http\JsonResponse;

class PaymentAllocationsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = PaymentAllocations::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No payment allocations found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocations retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(PaymentAllocationsRequest $request): JsonResponse
    {
        $data = PaymentAllocations::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocation created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = PaymentAllocations::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment allocation not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocation retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(PaymentAllocationsRequest $request, string $id): JsonResponse
    {
        $data = PaymentAllocations::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment allocation not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Payment allocation updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = PaymentAllocations::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment allocation not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Payment allocation deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
