<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodsRequest;
use App\Http\Models\PaymentMethods;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    public function index()
    {
        $data = PaymentMethods::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No payment methods found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment methods retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(PaymentMethodsRequest $request)
    {
        $data = PaymentMethods::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Payment method created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = PaymentMethods::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment method not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment method retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(PaymentMethodsRequest $request, string $id)
    {
        $data = PaymentMethods::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment method not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Payment method updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id)
    {
        $data = PaymentMethods::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment method not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Payment method deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
