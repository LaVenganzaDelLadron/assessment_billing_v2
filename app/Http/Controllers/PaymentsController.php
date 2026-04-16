<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentsRequest;
use App\Http\Models\Payments;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        $data = Payments::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No payments found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payments retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(PaymentsRequest $request)
    {
        $data = Payments::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Payment created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = Payments::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Payment retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(PaymentsRequest $request, string $id)
    {
        $data = Payments::query()->find($id);
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

    public function destroy(string $id)
    {
        $data = Payments::query()->find($id);
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
