<?php

namespace App\Http\Controllers;

use App\Http\Requests\RefundsRequest;
use App\Http\Models\Refunds;
use Illuminate\Http\Request;

class RefundsController extends Controller
{

    public function index()
    {
        $data = Refunds::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No refunds found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Refunds retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(RefundsRequest $request)
    {
        $data = Refunds::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Refund created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $data = Refunds::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Refund not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Refund retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(RefundsRequest $request, string $id)
    {
        $data = Refunds::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Refund not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Refund updated successfully.',
            'status' => 'success',
        ], 200);
    }


    public function destroy(string $id)
    {
        $data = Refunds::query()->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Refund not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Refund deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}
