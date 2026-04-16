<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogsRequest;
use App\Models\AuditLogs;
use Illuminate\Http\JsonResponse;

class AuditLogsController extends Controller
{

    public function index(): JsonResponse
    {
        $data = AuditLogs::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No audit logs found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Audit logs retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AuditLogsRequest $request): JsonResponse
    {
        $data = AuditLogs::create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Audit log created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = AuditLogs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Audit log not found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Audit log retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AuditLogsRequest $request, string $id): JsonResponse
    {
        $data = AuditLogs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Audit log not found.'], 404);
        }
        $data->update($request->validated());
        return response()->json([
            'data' => $data->fresh(),
            'message' => 'Audit log updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = AuditLogs::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Audit log not found.'], 404);
        }
        $data->delete();
        return response()->json([
            'message' => 'Audit log deleted successfully.',
            'status' => 'success',
        ], 200);
    }
}

