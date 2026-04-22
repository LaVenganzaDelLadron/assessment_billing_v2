<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTeacherSubjectRequest;
use App\Http\Requests\TeachersRequest;
use App\Models\Subjects;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teachers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class TeachersController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Teachers::with('user')->get();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No teachers found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Teachers retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function store(TeachersRequest $request): JsonResponse
    {
        $data = Teachers::create($request->validated());

        return response()->json([
            'data' => $data->load('user'),
            'message' => 'Teacher created successfully.',
            'status' => 'success',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $data = Teachers::with('user')->find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Teacher retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    public function update(TeachersRequest $request, string $id): JsonResponse
    {
        $data = Teachers::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }
        $data->update($request->validated());

        return response()->json([
            'data' => $data->fresh()->load('user'),
            'message' => 'Teacher updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $data = Teachers::find($id);
        if ($data === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }
        $data->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    public function assignSubject(AssignTeacherSubjectRequest $request, string $id): JsonResponse
    {
        $teacher = Teachers::query()
            ->with(['subjectAssignments.subject'])
            ->find($id);

        if ($teacher === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        $validated = $request->validated();

        $subject = Subjects::find($validated['subject_id']);
        if ($subject === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }

        $days = $this->normalizeDays($validated['days']);

        $conflictingAssignment = $this->findConflictingAssignment(
            assignments: $teacher->subjectAssignments,
            days: $days,
            startTime: $validated['start_time'],
            endTime: $validated['end_time'],
        );

        if ($conflictingAssignment !== null) {
            return response()->json([
                'message' => 'This teacher already has another subject assigned at the same day and time.',
                'conflict' => [
                    'assignment_id' => $conflictingAssignment->id,
                    'subject_id' => $conflictingAssignment->subject_id,
                    'subject_name' => $conflictingAssignment->subject?->name,
                    'days' => $conflictingAssignment->days,
                    'start_time' => $conflictingAssignment->start_time,
                    'end_time' => $conflictingAssignment->end_time,
                ],
            ], 422);
        }

        $assignment = SubjectTeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'days' => $days,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room' => $validated['room'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'data' => $assignment->load(['teacher.user', 'subject']),
            'message' => 'Subject assigned to teacher successfully.',
            'status' => 'success',
        ], 201);
    }

    public function updateAssignedSubject(
        AssignTeacherSubjectRequest $request,
        string $id,
        string $assignmentId
    ): JsonResponse {
        $teacher = Teachers::query()
            ->with(['subjectAssignments.subject'])
            ->find($id);

        if ($teacher === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        $assignment = SubjectTeacherAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->with(['teacher.user', 'subject'])
            ->find($assignmentId);

        if ($assignment === null) {
            return response()->json(['message' => 'Teacher subject assignment not found.'], 404);
        }

        $validated = $request->validated();

        $subject = isset($validated['subject_id'])
            ? Subjects::find($validated['subject_id'])
            : $assignment->subject;

        if ($subject === null) {
            return response()->json(['message' => 'Subject not found.'], 404);
        }

        $days = $this->normalizeDays($validated['days'] ?? $assignment->days ?? []);
        $startTime = $validated['start_time'] ?? $assignment->start_time;
        $endTime = $validated['end_time'] ?? $assignment->end_time;

        $conflictingAssignment = $this->findConflictingAssignment(
            assignments: $teacher->subjectAssignments,
            days: $days,
            startTime: $startTime,
            endTime: $endTime,
            ignoreAssignmentId: (int) $assignment->id,
        );

        if ($conflictingAssignment !== null) {
            return response()->json([
                'message' => 'This teacher already has another subject assigned at the same day and time.',
                'conflict' => [
                    'assignment_id' => $conflictingAssignment->id,
                    'subject_id' => $conflictingAssignment->subject_id,
                    'subject_name' => $conflictingAssignment->subject?->name,
                    'days' => $conflictingAssignment->days,
                    'start_time' => $conflictingAssignment->start_time,
                    'end_time' => $conflictingAssignment->end_time,
                ],
            ], 422);
        }

        $assignment->update([
            'subject_id' => $subject->id,
            'days' => $days,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => $validated['room'] ?? $assignment->room,
            'status' => $validated['status'] ?? $assignment->status,
        ]);

        return response()->json([
            'data' => $assignment->fresh()->load(['teacher.user', 'subject']),
            'message' => 'Teacher subject assignment updated successfully.',
            'status' => 'success',
        ], 200);
    }

    public function destroyAssignedSubject(string $id, string $assignmentId): JsonResponse
    {
        $teacher = Teachers::find($id);

        if ($teacher === null) {
            return response()->json(['message' => 'Teacher not found.'], 404);
        }

        $assignment = SubjectTeacherAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->find($assignmentId);

        if ($assignment === null) {
            return response()->json(['message' => 'Teacher subject assignment not found.'], 404);
        }

        $assignment->delete();

        return response()->json([
            'message' => 'Teacher subject assignment deleted successfully.',
            'status' => 'success',
        ], 200);
    }

    /**
     * @param  array<int, SubjectTeacherAssignment>|Collection<int, SubjectTeacherAssignment>  $assignments
     * @param  array<int, string>  $days
     */
    private function findConflictingAssignment(
        iterable $assignments,
        array $days,
        string $startTime,
        string $endTime,
        ?int $ignoreAssignmentId = null,
    ): ?SubjectTeacherAssignment {
        foreach ($assignments as $assignment) {
            if ($ignoreAssignmentId !== null && (int) $assignment->id === $ignoreAssignmentId) {
                continue;
            }

            if ($this->hasScheduleConflict(
                existingDays: $assignment->days ?? [],
                newDays: $days,
                existingStartTime: $assignment->start_time,
                existingEndTime: $assignment->end_time,
                newStartTime: $startTime,
                newEndTime: $endTime,
            )) {
                return $assignment;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $days
     * @return array<int, string>
     */
    private function normalizeDays(array $days): array
    {
        return collect($days)
            ->map(fn (string $day): string => trim($day))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $existingDays
     * @param  array<int, string>  $newDays
     */
    private function hasScheduleConflict(
        array $existingDays,
        array $newDays,
        string $existingStartTime,
        string $existingEndTime,
        string $newStartTime,
        string $newEndTime,
    ): bool {
        if (count(array_intersect($existingDays, $newDays)) === 0) {
            return false;
        }

        return $newStartTime < $existingEndTime && $newEndTime > $existingStartTime;
    }
}
