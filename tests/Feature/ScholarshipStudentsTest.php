<?php

namespace Tests\Feature;

use App\Models\Scholarship;
use App\Models\StudentScholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScholarshipStudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_students_with_scholarships(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'BS Information Technology',
            'department' => 'College of Computing',
            'code' => 'BSIT',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentId = DB::table('students')->insertGetId([
            'user_id' => $studentUser->id,
            'student_no' => '2026-0001',
            'first_name' => 'Jane',
            'middle_name' => 'Q',
            'last_name' => 'Doe',
            'program_id' => $programId,
            'year_level' => 2,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Academic Excellence',
            'description' => 'Merit-based scholarship',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'is_active' => true,
        ]);

        StudentScholarship::create([
            'student_id' => $studentId,
            'scholarship_id' => $scholarship->id,
            'discount_type' => 'percent',
            'discount_value' => 50,
            'original_amount' => 10000,
            'discount_amount' => 5000,
            'final_amount' => 5000,
            'applied_at' => now(),
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/scholarships/students');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Students with scholarships retrieved successfully.')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.student.id', $studentId)
            ->assertJsonPath('data.0.scholarship.id', $scholarship->id);
    }

    public function test_students_with_scholarships_returns_not_found_when_empty(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/scholarships/students');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'No students with scholarships found.',
            ]);
    }
}
