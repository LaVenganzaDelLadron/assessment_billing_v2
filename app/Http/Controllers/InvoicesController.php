<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoicesRequest;
use App\Models\Invoices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Invoices::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No invoices found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoices retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentIndex(Request $request): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = Invoices::query()
            ->with(['assessment', 'invoiceLines', 'payments'])
            ->where('student_id', $student->id)
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No invoices found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoices retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(InvoicesRequest $request): JsonResponse
    {
        $data = Invoices::create($request->validated());

        return response()->json([
            'data' => $data,
            'message' => 'Invoice created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Invoices::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentShow(Request $request, string $id): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = Invoices::query()
            ->with(['assessment', 'invoiceLines', 'payments'])
            ->where('student_id', $student->id)
            ->find($id);

        if ($data === null) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(InvoicesRequest $request, string $id): JsonResponse
    {
        $data = Invoices::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Invoice updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Invoices::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
