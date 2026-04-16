<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentAllocationsRequest;
use App\Http\Models\PaymentAllocations;
use Illuminate\Http\Request;

class PaymentAllocationsController extends Controller
{

    public function index()
    {
        $data = PaymentAllocations::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No payment allocations found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocations retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(PaymentAllocationsRequest $request)
    {
        $data = PaymentAllocations::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocation created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = PaymentAllocations::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment allocation not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment allocation retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(PaymentAllocationsRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = PaymentAllocations::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Payment allocation not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Payment allocation updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = PaymentAllocations::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Payment allocation not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Payment allocation deleted successfully',
        ], 200);
    }
}
