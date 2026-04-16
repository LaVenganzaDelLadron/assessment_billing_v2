<?php

namespace App\Http\Controllers;


use App\Http\Requests\OfficialReceiptsRequest;
use App\Http\Requests\OfficialReceipts;
use Illuminate\Http\Request;

class OfficialReceiptsController extends Controller
{

    public function index()
    {
        $data = OfficialReceipts::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No official receipts found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Official receipts retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(OfficialReceiptsRequest $request)
    {
        $data = OfficialReceipts::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Official receipt created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $officialReceipt = OfficialReceipts::query()->find($id);
        if ($officialReceipt === null) {
            return response()->json(['message' => 'Official receipt not found.'], 404);
        }
        return response()->json([
            'data' => $officialReceipt,
            'message' => 'Official receipt retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(OfficialReceiptsRequest $request, string $id)
    {
        $validate = $request->validated();
        $data = OfficialReceipts::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Official receipt not found',
            ], 404);
        }
        $data->update($validate);

        return response()->json([
            'message' => 'Official receipt updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = OfficialReceipts::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Official receipt not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Official receipt deleted successfully',
        ], 200);
    }
}
