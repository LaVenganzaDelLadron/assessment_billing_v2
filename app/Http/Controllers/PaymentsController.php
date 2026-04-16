<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentsRequest;
use App\Models\Payments;
use Illuminate\Http\JsonResponse;

class PaymentsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Payments::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No payments found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(PaymentsRequest $request): JsonResponse
    {
        $data = Payments::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Payment created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Payments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(PaymentsRequest $request, string $id): JsonResponse
    {
        $data = Payments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Payment updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Payments::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Payment deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
