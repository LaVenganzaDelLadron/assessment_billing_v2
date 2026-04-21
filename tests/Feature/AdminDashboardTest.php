<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_can_access_dashboard_stats(): void
    {
        $admin = User::factory()->make([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'students',
                    'teachers',
                    'programs',
                    'subjects',
                    'assessments',
                    'enrollments',
                    'invoices',
                    'payments',
                    'official_receipts',
                ],
                'message',
                'status',
            ]);
    }
}
