<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentOwnDataAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_only_view_their_own_records_across_student_routes(): void
    {
        $data = $this->seedStudentData();

        Sanctum::actingAs($data['current_user'], ['*']);

        $this->getJson('/api/student/students/me')
            ->assertOk()
            ->assertJsonPath('data.id', $data['current_student_id']);

        $this->getJson('/api/student/enrollments')
            ->assertOk()
            ->assertJsonPath('data.0.student.id', $data['current_student_id'])
            ->assertJsonPath('data.0.subjects.0.id', $data['current_subject_id']);

        $this->getJson("/api/student/enrollments/{$data['current_enrollment_id']}")
            ->assertOk()
            ->assertJsonPath('data.student_id', $data['current_student_id']);

        $this->getJson("/api/student/enrollments/{$data['other_enrollment_id']}")
            ->assertNotFound();

        $this->getJson('/api/student/invoices')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.student_id', $data['current_student_id']);

        $this->getJson("/api/student/invoices/{$data['current_invoice_id']}")
            ->assertOk()
            ->assertJsonPath('data.student_id', $data['current_student_id']);

        $this->getJson("/api/student/invoices/{$data['other_invoice_id']}")
            ->assertNotFound();

        $this->getJson('/api/student/invoice-lines')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.invoice_id', $data['current_invoice_id']);

        $this->getJson("/api/student/invoice-lines/{$data['current_invoice_line_id']}")
            ->assertOk()
            ->assertJsonPath('data.invoice_id', $data['current_invoice_id']);

        $this->getJson("/api/student/invoice-lines/{$data['other_invoice_line_id']}")
            ->assertNotFound();

        $this->getJson('/api/student/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.invoice_id', $data['current_invoice_id']);

        $this->getJson("/api/student/payments/{$data['current_payment_id']}")
            ->assertOk()
            ->assertJsonPath('data.invoice_id', $data['current_invoice_id']);

        $this->getJson("/api/student/payments/{$data['other_payment_id']}")
            ->assertNotFound();

        $this->getJson('/api/student/official-receipts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.payment_id', $data['current_payment_id']);

        $this->getJson("/api/student/official-receipts/{$data['current_receipt_id']}")
            ->assertOk()
            ->assertJsonPath('data.payment_id', $data['current_payment_id']);

        $this->getJson("/api/student/official-receipts/{$data['other_receipt_id']}")
            ->assertNotFound();

        $this->getJson('/api/student/payment-methods')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $data['active_payment_method_id']);

        $this->getJson("/api/student/payment-methods/{$data['active_payment_method_id']}")
            ->assertOk()
            ->assertJsonPath('data.id', $data['active_payment_method_id']);

        $this->getJson("/api/student/payment-methods/{$data['inactive_payment_method_id']}")
            ->assertNotFound();
    }

    /**
     * @return array<string, int|User>
     */
    private function seedStudentData(): array
    {
        $currentUser = User::factory()->create([
            'role' => 'student',
        ]);

        $otherUser = User::factory()->create([
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

        $academicTermId = DB::table('academic_terms')->insertGetId([
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'start_date' => '2026-06-01',
            'end_date' => '2026-10-15',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentStudentId = DB::table('students')->insertGetId([
            'user_id' => $currentUser->id,
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

        $otherStudentId = DB::table('students')->insertGetId([
            'user_id' => $otherUser->id,
            'student_no' => '2026-0002',
            'first_name' => 'Maria',
            'middle_name' => 'T',
            'last_name' => 'Santos',
            'program_id' => $programId,
            'year_level' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentSubjectId = DB::table('subjects')->insertGetId([
            'code' => 'IT101',
            'subject_code' => 'IT101',
            'name' => 'Introduction to Computing',
            'units' => 3,
            'type' => 'Lecture',
            'status' => 'active',
            'program_id' => $programId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherSubjectId = DB::table('subjects')->insertGetId([
            'code' => 'IT102',
            'subject_code' => 'IT102',
            'name' => 'Computer Programming 1',
            'units' => 3,
            'type' => 'Lecture',
            'status' => 'active',
            'program_id' => $programId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentEnrollmentId = DB::table('enrollments')->insertGetId([
            'student_id' => $currentStudentId,
            'subject_id' => $currentSubjectId,
            'academic_term_id' => $academicTermId,
            'status' => 'enrolled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherEnrollmentId = DB::table('enrollments')->insertGetId([
            'student_id' => $otherStudentId,
            'subject_id' => $otherSubjectId,
            'academic_term_id' => $academicTermId,
            'status' => 'enrolled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentAssessmentId = DB::table('assessments')->insertGetId([
            'student_id' => $currentStudentId,
            'academic_term_id' => $academicTermId,
            'total_units' => 18,
            'status' => 'finalized',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherAssessmentId = DB::table('assessments')->insertGetId([
            'student_id' => $otherStudentId,
            'academic_term_id' => $academicTermId,
            'total_units' => 18,
            'status' => 'finalized',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentInvoiceId = DB::table('invoices')->insertGetId([
            'student_id' => $currentStudentId,
            'assessment_id' => $currentAssessmentId,
            'invoice_number' => 'INV-1001',
            'total_amount' => 10000,
            'balance' => 5000,
            'due_date' => '2026-07-15',
            'status' => 'partial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherInvoiceId = DB::table('invoices')->insertGetId([
            'student_id' => $otherStudentId,
            'assessment_id' => $otherAssessmentId,
            'invoice_number' => 'INV-1002',
            'total_amount' => 12000,
            'balance' => 12000,
            'due_date' => '2026-07-15',
            'status' => 'unpaid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentInvoiceLineId = DB::table('invoice_lines')->insertGetId([
            'invoice_id' => $currentInvoiceId,
            'line_type' => 'tuition',
            'subject_id' => $currentSubjectId,
            'description' => 'Tuition fee',
            'quantity' => 3,
            'unit_price' => 1000,
            'amount' => 3000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherInvoiceLineId = DB::table('invoice_lines')->insertGetId([
            'invoice_id' => $otherInvoiceId,
            'line_type' => 'tuition',
            'subject_id' => $otherSubjectId,
            'description' => 'Tuition fee',
            'quantity' => 3,
            'unit_price' => 1200,
            'amount' => 3600,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $activePaymentMethodId = DB::table('payment_methods')->insertGetId([
            'code' => 'CASH',
            'name' => 'Cash',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $inactivePaymentMethodId = DB::table('payment_methods')->insertGetId([
            'code' => 'OLD_METHOD',
            'name' => 'Old Method',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentPaymentId = DB::table('payments')->insertGetId([
            'invoice_id' => $currentInvoiceId,
            'amount_paid' => 5000,
            'payment_method_id' => $activePaymentMethodId,
            'reference_number' => 'REF-1001',
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherPaymentId = DB::table('payments')->insertGetId([
            'invoice_id' => $otherInvoiceId,
            'amount_paid' => 2000,
            'payment_method_id' => $activePaymentMethodId,
            'reference_number' => 'REF-1002',
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $currentReceiptId = DB::table('official_receipts')->insertGetId([
            'payment_id' => $currentPaymentId,
            'or_number' => 'OR-1001',
            'issued_by' => 'Cashier 1',
            'issued_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherReceiptId = DB::table('official_receipts')->insertGetId([
            'payment_id' => $otherPaymentId,
            'or_number' => 'OR-1002',
            'issued_by' => 'Cashier 2',
            'issued_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'current_user' => $currentUser,
            'current_student_id' => $currentStudentId,
            'other_student_id' => $otherStudentId,
            'current_subject_id' => $currentSubjectId,
            'current_enrollment_id' => $currentEnrollmentId,
            'other_enrollment_id' => $otherEnrollmentId,
            'current_invoice_id' => $currentInvoiceId,
            'other_invoice_id' => $otherInvoiceId,
            'current_invoice_line_id' => $currentInvoiceLineId,
            'other_invoice_line_id' => $otherInvoiceLineId,
            'active_payment_method_id' => $activePaymentMethodId,
            'inactive_payment_method_id' => $inactivePaymentMethodId,
            'current_payment_id' => $currentPaymentId,
            'other_payment_id' => $otherPaymentId,
            'current_receipt_id' => $currentReceiptId,
            'other_receipt_id' => $otherReceiptId,
        ];
    }
}
