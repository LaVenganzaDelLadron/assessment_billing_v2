<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogsRequest;
use App\Http\Models\AuditLogs;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{

    public function index()
    {
        $data = AuditLogs::query()->get();
        if($data->isEmpty()) {
            return response()->json(['message' => 'No audit logs found.'], 404);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Audit logs retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(AuditLogsRequest $request)
    {
        $data = AuditLogs::query()->create($request->validated());
        return response()->json([
            'data' => $data,
            'message' => 'Audit log created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id)
    {
        $auditLog = AuditLogs::query()->find($id);
        if ($auditLog === null) {
            return response()->json(['message' => 'Audit log not found.'], 404);
        }
        return response()->json([
            'data' => $auditLog,
            'message' => 'Audit log retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(AuditLogsRequest $request, string $id)
    {
        $data = $request->validated();
        $data = AuditLogs::query()->find($id);

        if (! $data) {
            return response()->json([
                'message' => 'Audit log not found',
            ], 404);
        }
        $data->update($data);

        return response()->json([
            'message' => 'Audit log updated successfully',
            'data' => $data->fresh(),
        ], 201);
    }

    public function destroy(string $id)
    {
        $data = AuditLogs::query()->find($id);
        if (! $data) {
            return response()->json([
                'message' => 'Audit log not found',
            ], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Audit log deleted successfully',
        ], 200);

    }
}

