<?php

namespace Tests\Feature;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    /**
     * Verify the demo seeder populates every repository table with sample data.
     */
    public function test_demo_data_seeder_populates_all_application_tables(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('DemoDataSeederTest requires PostgreSQL because the current migrations are not SQLite compatible.');
        }

        $this->seed(DemoDataSeeder::class);

        $tables = [
            'academic_terms',
            'assessment_breakdown',
            'assessments',
            'audit_logs',
            'cache',
            'cache_locks',
            'enrollments',
            'failed_jobs',
            'fee_structure',
            'invoice_lines',
            'invoices',
            'job_batches',
            'jobs',
            'migrations',
            'official_receipts',
            'password_reset_tokens',
            'payment_allocations',
            'payment_methods',
            'payments',
            'personal_access_tokens',
            'program_subject',
            'programs',
            'refunds',
            'sessions',
            'students',
            'subjects',
            'teachers',
            'users',
        ];

        foreach ($tables as $table) {
            $this->assertGreaterThan(0, DB::table($table)->count(), "Expected [{$table}] to contain seeded data.");
        }

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('students', [
            'student_no' => '2026-0001',
        ]);

        $this->assertDatabaseHas('programs', [
            'code' => 'BSIT',
        ]);

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-DEMO-2026-0002',
            'status' => 'partial',
        ]);

        $this->assertDatabaseHas('refunds', [
            'reason' => 'Duplicate card charge detected',
            'status' => 'approved',
        ]);
    }
}
