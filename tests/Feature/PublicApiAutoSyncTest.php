<?php

namespace Tests\Feature;

use App\Models\Programs;
use App\Models\Subjects;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicApiAutoSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_programs_endpoint_mirrors_remote_programs_into_local_database(): void
    {
        Programs::create([
            'custom_id' => 'PRG000060',
            'external_id' => 60,
            'code' => 'OLD-BSIT',
            'name' => 'Old Program Name',
            'department' => 'OLD',
            'status' => 'inactive',
        ]);

        Programs::create([
            'custom_id' => 'PRG000999',
            'external_id' => 999,
            'code' => 'OLD',
            'name' => 'Should Be Deleted',
            'department' => 'OLD',
            'status' => 'inactive',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://registrarmodule1-production.up.railway.app/api/programs' => Http::response([
                [
                    'id' => 60,
                    'code' => 'BSIT',
                    'name' => 'Bachelor of Science in Information Technology',
                    'department' => 'CCS',
                    'status' => 'active',
                    'created_at' => '2026-04-16T19:57:46.000000Z',
                    'updated_at' => '2026-04-16T19:57:46.000000Z',
                ],
                [
                    'id' => 61,
                    'code' => 'BSCS',
                    'name' => 'Bachelor of Science in Computer Science',
                    'department' => 'CCS',
                    'status' => 'active',
                    'created_at' => '2026-04-16T19:59:21.000000Z',
                    'updated_at' => '2026-04-16T19:59:21.000000Z',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/programs');

        $response->assertOk()
            ->assertJsonPath('0.id', 60)
            ->assertJsonPath('0.code', 'BSIT')
            ->assertJsonPath('0.name', 'Bachelor of Science in Information Technology')
            ->assertJsonPath('0.department', 'CCS')
            ->assertJsonPath('0.status', 'active')
            ->assertJsonPath('1.id', 61)
            ->assertJsonPath('1.code', 'BSCS');

        $this->assertDatabaseHas('programs', [
            'external_id' => 60,
            'code' => 'BSIT',
            'name' => 'Bachelor of Science in Information Technology',
        ]);

        $this->assertDatabaseHas('programs', [
            'external_id' => 61,
            'code' => 'BSCS',
            'name' => 'Bachelor of Science in Computer Science',
        ]);

        $this->assertDatabaseMissing('programs', [
            'external_id' => 999,
        ]);
    }

    public function test_public_subjects_endpoint_mirrors_remote_subjects_and_relationships_into_local_database(): void
    {
        $existingProgram = Programs::create([
            'custom_id' => 'PRG000060',
            'external_id' => 60,
            'code' => 'OLD-BSIT',
            'name' => 'Old Program Name',
            'department' => 'OLD',
            'status' => 'inactive',
        ]);

        Programs::create([
            'custom_id' => 'PRG000999',
            'external_id' => 999,
            'code' => 'OLD',
            'name' => 'Old Program To Delete',
            'department' => 'OLD',
            'status' => 'inactive',
        ]);

        $existingSubject = Subjects::create([
            'custom_id' => 'SUB000016',
            'external_id' => 16,
            'code' => 'OLD101',
            'subject_code' => 'OLD101',
            'name' => 'Old Subject Name',
            'units' => 2,
            'type' => 'lab',
            'status' => 'inactive',
        ]);

        $existingSubject->programs()->attach($existingProgram->id, [
            'year_level' => '9',
            'semester' => 'Summer',
            'school_year' => '2020-2021',
            'status' => 'inactive',
        ]);

        Subjects::create([
            'custom_id' => 'SUB000999',
            'external_id' => 999,
            'code' => 'OLD999',
            'subject_code' => 'OLD999',
            'name' => 'Old Subject To Delete',
            'units' => 1,
            'type' => 'lecture',
            'status' => 'inactive',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://registrarmodule1-production.up.railway.app/api/programs' => Http::response([
                [
                    'id' => 60,
                    'code' => 'BSIT',
                    'name' => 'Bachelor of Science in Information Technology',
                    'department' => 'CCS',
                    'status' => 'active',
                    'created_at' => '2026-04-16T19:57:46.000000Z',
                    'updated_at' => '2026-04-16T19:57:46.000000Z',
                ],
                [
                    'id' => 61,
                    'code' => 'BSCS',
                    'name' => 'Bachelor of Science in Computer Science',
                    'department' => 'CCS',
                    'status' => 'active',
                    'created_at' => '2026-04-16T19:59:21.000000Z',
                    'updated_at' => '2026-04-16T19:59:21.000000Z',
                ],
            ], 200),
            'https://registrarmodule1-production.up.railway.app/api/subjects' => Http::response([
                [
                    'id' => 16,
                    'subject_code' => 'IT101',
                    'subject_name' => 'Intro to Computing',
                    'units' => 3,
                    'type' => 'lecture',
                    'status' => 'active',
                    'created_at' => '2026-04-16T20:18:46.000000Z',
                    'updated_at' => '2026-04-16T20:18:46.000000Z',
                    'programs' => [
                        [
                            'id' => 60,
                            'code' => 'BSIT',
                            'name' => 'Bachelor of Science in Information Technology',
                            'department' => 'CCS',
                            'status' => 'active',
                            'created_at' => '2026-04-16T19:57:46.000000Z',
                            'updated_at' => '2026-04-16T19:57:46.000000Z',
                            'pivot' => [
                                'year_level' => '1',
                                'semester' => '1st',
                                'school_year' => '2026-2027',
                                'status' => 'active',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 17,
                    'subject_code' => 'IT102',
                    'subject_name' => 'Computer Programming 1',
                    'units' => 3,
                    'type' => 'lecture',
                    'status' => 'active',
                    'created_at' => '2026-04-16T20:22:26.000000Z',
                    'updated_at' => '2026-04-16T20:22:26.000000Z',
                    'programs' => [],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/subjects');

        $response->assertOk()
            ->assertJsonPath('0.id', 16)
            ->assertJsonPath('0.subject_code', 'IT101')
            ->assertJsonPath('0.subject_name', 'Intro to Computing')
            ->assertJsonPath('0.programs.0.id', 60)
            ->assertJsonPath('0.programs.0.pivot.year_level', '1')
            ->assertJsonPath('0.programs.0.pivot.semester', '1st')
            ->assertJsonPath('1.id', 17)
            ->assertJsonPath('1.subject_code', 'IT102');

        $this->assertDatabaseHas('subjects', [
            'external_id' => 16,
            'subject_code' => 'IT101',
            'name' => 'Intro to Computing',
        ]);

        $this->assertDatabaseHas('subjects', [
            'external_id' => 17,
            'subject_code' => 'IT102',
            'name' => 'Computer Programming 1',
        ]);

        $this->assertDatabaseMissing('subjects', [
            'external_id' => 999,
        ]);

        $this->assertDatabaseMissing('programs', [
            'external_id' => 999,
        ]);

        $program = Programs::where('external_id', 60)->firstOrFail();
        $subject = Subjects::where('external_id', 16)->firstOrFail();

        $this->assertDatabaseHas('program_subject', [
            'program_id' => $program->id,
            'subject_id' => $subject->id,
            'year_level' => '1',
            'semester' => '1st',
            'school_year' => '2026-2027',
            'status' => 'active',
        ]);
    }
}
