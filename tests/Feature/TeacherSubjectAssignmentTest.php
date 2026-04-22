<?php

namespace Tests\Feature;

use App\Models\SubjectTeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeacherSubjectAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_teacher_subject_assignment(): void
    {
        [$admin, $teacherId] = $this->createTeacher();
        $firstSubjectId = $this->createSubject('IT101', 'Introduction to Computing');
        $secondSubjectId = $this->createSubject('IT102', 'Computer Programming 1');

        $assignment = SubjectTeacherAssignment::create([
            'teacher_id' => $teacherId,
            'subject_id' => $firstSubjectId,
            'days' => ['Monday', 'Wednesday'],
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room' => 'Lab 1',
            'status' => 'active',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->putJson("/api/admin/teachers/{$teacherId}/assign-subject/{$assignment->id}", [
            'subject_id' => $secondSubjectId,
            'days' => ['Tuesday', 'Thursday'],
            'start_time' => '13:00',
            'end_time' => '15:00',
            'room' => 'Lab 2',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Teacher subject assignment updated successfully.')
            ->assertJsonPath('data.id', $assignment->id)
            ->assertJsonPath('data.subject_id', $secondSubjectId)
            ->assertJsonPath('data.days.0', 'Tuesday')
            ->assertJsonPath('data.days.1', 'Thursday')
            ->assertJsonPath('data.start_time', '13:00')
            ->assertJsonPath('data.end_time', '15:00')
            ->assertJsonPath('data.room', 'Lab 2');
    }

    public function test_admin_can_delete_a_teacher_subject_assignment(): void
    {
        [$admin, $teacherId] = $this->createTeacher();
        $subjectId = $this->createSubject('IT101', 'Introduction to Computing');

        $assignment = SubjectTeacherAssignment::create([
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'days' => ['Monday', 'Wednesday'],
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room' => 'Lab 1',
            'status' => 'active',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson("/api/admin/teachers/{$teacherId}/assign-subject/{$assignment->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Teacher subject assignment deleted successfully.',
                'status' => 'success',
            ]);

        $this->assertDatabaseMissing('subject_teacher_assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_admin_can_assign_subject_to_teacher_when_schedule_is_available(): void
    {
        [$admin, $teacherId] = $this->createTeacher();
        $subjectId = $this->createSubject('IT101', 'Introduction to Computing');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson("/api/admin/teachers/{$teacherId}/assign-subject", [
            'subject_id' => $subjectId,
            'days' => ['Monday', 'Wednesday'],
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room' => 'Lab 1',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Subject assigned to teacher successfully.')
            ->assertJsonPath('data.teacher_id', $teacherId)
            ->assertJsonPath('data.subject_id', $subjectId)
            ->assertJsonPath('data.days.0', 'Monday')
            ->assertJsonPath('data.days.1', 'Wednesday')
            ->assertJsonPath('data.start_time', '08:00')
            ->assertJsonPath('data.end_time', '10:00');
    }

    public function test_admin_cannot_assign_subject_when_teacher_has_a_time_conflict(): void
    {
        [$admin, $teacherId] = $this->createTeacher();
        $firstSubjectId = $this->createSubject('IT101', 'Introduction to Computing');
        $secondSubjectId = $this->createSubject('IT102', 'Computer Programming 1');

        SubjectTeacherAssignment::create([
            'teacher_id' => $teacherId,
            'subject_id' => $firstSubjectId,
            'days' => ['Monday', 'Wednesday'],
            'start_time' => '09:00',
            'end_time' => '11:00',
            'room' => 'Lab 1',
            'status' => 'active',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson("/api/admin/teachers/{$teacherId}/assign-subject", [
            'subject_id' => $secondSubjectId,
            'days' => ['Wednesday'],
            'start_time' => '10:00',
            'end_time' => '12:00',
            'room' => 'Lab 2',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'This teacher already has another subject assigned at the same day and time.')
            ->assertJsonPath('conflict.subject_id', $firstSubjectId)
            ->assertJsonPath('conflict.subject_name', 'Introduction to Computing');
    }

    /**
     * @return array{0: User, 1: int}
     */
    private function createTeacher(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $teacherUser = User::factory()->create([
            'role' => 'teacher',
        ]);

        $teacherId = DB::table('teachers')->insertGetId([
            'user_id' => $teacherUser->id,
            'teacher_id' => 'T-1001',
            'first_name' => 'Maria',
            'middle_name' => 'S',
            'last_name' => 'Santos',
            'department' => 'CCS',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$admin, $teacherId];
    }

    private function createSubject(string $code, string $name): int
    {
        $programId = DB::table('programs')->insertGetId([
            'name' => 'Bachelor of Science in Information Technology '.$code,
            'department' => 'CCS',
            'code' => 'BSIT-'.$code,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('subjects')->insertGetId([
            'code' => $code,
            'subject_code' => $code,
            'name' => $name,
            'units' => 3,
            'type' => 'Lecture',
            'status' => 'active',
            'program_id' => $programId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
