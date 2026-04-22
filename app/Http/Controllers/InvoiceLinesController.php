<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceLinesRequest;
use App\Models\InvoiceLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceLinesController extends Controller
{
    public function index(): JsonResponse
    {
        $data = InvoiceLine::with(['invoice', 'subject'])->get();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No invoice lines found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice lines retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentIndex(Request $request): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = InvoiceLine::query()
            ->with(['invoice', 'subject'])
            ->whereHas('invoice', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No invoice lines found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice lines retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(InvoiceLinesRequest $request): JsonResponse
    {
        $data = InvoiceLine::create($request->validated());

        return response()->json([
            'data' => $data->load(['invoice', 'subject']),
            'message' => 'Invoice line created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = InvoiceLine::with(['invoice', 'subject'])->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice line not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice line retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function studentShow(Request $request, string $id): JsonResponse
    {
        $student = $this->authenticatedStudent($request);

        if ($student === null) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = InvoiceLine::query()
            ->with(['invoice', 'subject'])
            ->whereHas('invoice', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->find($id);

        if ($data === null) {
            return response()->json(['message' => 'Invoice line not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Invoice line retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(InvoiceLinesRequest $request, string $id): JsonResponse
    {
        $data = InvoiceLine::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice line not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh()->load(['invoice', 'subject']),
            'message' => 'Invoice line updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = InvoiceLine::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice line not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Invoice line deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
