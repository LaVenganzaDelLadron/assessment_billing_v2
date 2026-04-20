<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateTeacherProfileRequest;
use App\Models\Programs;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get the authenticated student's profile.
     */
    public function getStudentProfile(Request $request): JsonResponse
    {
        $student = Students::where('user_id', $request->user()->id)
            ->with('program')
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json([
            'data' => $student,
        ], 200);
    }

    /**
     * Update the authenticated student's profile.
     */
    public function updateStudentProfile(UpdateStudentProfileRequest $request): JsonResponse
    {
        $student = Students::where('user_id', $request->user()->id)->firstOrFail();

        $student->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $student->load('program'),
        ], 200);
    }

    /**
     * Get the authenticated teacher's profile.
     */
    public function getTeacherProfile(Request $request): JsonResponse
    {
        $teacher = Teachers::where('user_id', $request->user()->id)->first();

        if (! $teacher) {
            return response()->json([
                'message' => 'Profile not found',
            ], 404);
        }

        return response()->json([
            'data' => $teacher,
        ], 200);
    }

    /**
     * Update the authenticated teacher's profile.
     */
    public function updateTeacherProfile(UpdateTeacherProfileRequest $request): JsonResponse
    {
        $teacher = Teachers::where('user_id', $request->user()->id)->firstOrFail();

        $teacher->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $teacher,
        ], 200);
    }

    /**
     * List all programs for selection in profile forms.
     */
    public function listPrograms(): JsonResponse
    {
        $programs = Programs::where('status', 'active')
            ->select('id', 'name', 'code', 'department')
            ->get();

        return response()->json([
            'data' => $programs,
        ], 200);
    }
}
