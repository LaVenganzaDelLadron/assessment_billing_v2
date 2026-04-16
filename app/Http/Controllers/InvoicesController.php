<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Models\Invoice;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{

    public function index()
    {
        $data = Invoice::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No invoices found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Invoices retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(InvoiceRequest $request)
    {
        $data = Invoice::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Invoice created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = Invoice::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Invoice retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(InvoiceRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = Invoice::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Invoice not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = Invoice::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Invoice not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ], 200);
    }
}
