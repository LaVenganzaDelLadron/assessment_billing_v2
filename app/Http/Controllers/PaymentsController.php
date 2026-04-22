<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentsRequest;
use App\Models\Payments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function studentIndex(Request $request): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = Payments::query()
            ->with(['invoice', 'paymentMethod', 'officialReceipt'])
            ->whereHas('invoice', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->get();

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

    public function studentShow(Request $request, string $id): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = Payments::query()
            ->with(['invoice', 'paymentMethod', 'officialReceipt'])
            ->whereHas('invoice', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->find($id);

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
