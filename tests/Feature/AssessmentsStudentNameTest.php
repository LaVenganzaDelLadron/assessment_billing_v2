<?php

namespace Tests\Feature;

use App\Models\Assessments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssessmentsStudentNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_assessments_with_student_name(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'Bachelor of Science in Information Technology',
            'department' => 'CCS',
            'code' => 'BSIT',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentId = DB::table('students')->insertGetId([
            'user_id' => $studentUser->id,
            'student_no' => '2026-0001',
            'first_name' => 'Juan',
            'middle_name' => 'S',
            'last_name' => 'Dela Cruz',
            'program_id' => $programId,
            'year_level' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $academicTermId = DB::table('academic_terms')->insertGetId([
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'start_date' => '2026-06-01',
            'end_date' => '2026-10-15',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $assessment = Assessments::create([
            'student_id' => $studentId,
            'academic_term_id' => $academicTermId,
            'total_units' => 18,
            'status' => 'finalized',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/assessments');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.id', $assessment->id)
            ->assertJsonPath('data.0.student.id', $studentId)
            ->assertJsonPath('data.0.student.student_no', '2026-0001')
            ->assertJsonPath('data.0.student.name', 'Juan Dela Cruz')
            ->assertJsonPath('data.0.academic_term.id', $academicTermId)
            ->assertJsonPath('data.0.academic_term.school_year', '2026-2027')
            ->assertJsonPath('data.0.academic_term.semester', '1st Semester');
    }
}
