<?php

namespace Tests\Feature;

use App\Models\FeeStructure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeeStructureProgramNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_fee_structures_with_program_details(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'Bachelor of Science in Information Technology',
            'department' => 'CCS',
            'code' => 'BSIT',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $feeStructure = FeeStructure::create([
            'program_id' => $programId,
            'fee_type' => 'registration',
            'amount' => 750,
            'per_unit' => false,
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/fee-structures');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.id', $feeStructure->id)
            ->assertJsonPath('data.0.program.id', $programId)
            ->assertJsonPath('data.0.program.name', 'Bachelor of Science in Information Technology');
    }

    public function test_admin_can_view_fee_structures_with_program_names(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'Bachelor of Science in Computer Science',
            'department' => 'CCS',
            'code' => 'BSCS',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FeeStructure::create([
            'program_id' => $programId,
            'fee_type' => 'tuition',
            'amount' => 1500,
            'per_unit' => true,
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/fee-structures/program-names');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.program_id', $programId)
            ->assertJsonPath('data.0.program_name', 'Bachelor of Science in Computer Science');
    }
}
