<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private CarbonImmutable $seededAt;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seededAt = CarbonImmutable::now()->startOfMinute();
        $users = $this->seedUsers();
        $terms = $this->seedAcademicTerms();
        $programs = $this->seedPrograms();
        $subjects = $this->seedSubjects($programs);
        $paymentMethods = $this->seedPaymentMethods();

        $this->seedProgramSubject($programs, $subjects);
        $this->seedTeachers($users);

        $students = $this->seedStudents($users, $programs);
        $feeStructures = $this->seedFeeStructures($programs);

        $this->seedEnrollments($students, $subjects, $terms);

        $assessments = $this->seedAssessments($students, $terms, $subjects, $feeStructures);
        $this->seedAssessmentBreakdowns($assessments, $students, $subjects, $feeStructures);

        $invoices = $this->seedInvoices($assessments, $students, $subjects, $feeStructures);
        $this->seedInvoiceLines($invoices, $students, $subjects, $feeStructures);

        $payments = $this->seedPayments($invoices, $paymentMethods);
        $refunds = $this->seedRefunds($payments);

        $this->seedPaymentAllocations($payments, $invoices);
        $this->seedOfficialReceipts($payments);
        $this->syncInvoiceBalances($invoices);
        $this->seedAuditLogs($users, $programs, $assessments, $invoices, $payments, $refunds);
        $this->seedSupportTables($users);
    }

    /**
     * @return array<int|string, object>
     */
    private function seedUsers(): array
    {
        $password = Hash::make('password');

        $rows = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'adminseed1',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.teacher@example.com',
                'password' => $password,
                'role' => 'teacher',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'teacher001',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'name' => 'Jose Reyes',
                'email' => 'jose.teacher@example.com',
                'password' => $password,
                'role' => 'teacher',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'teacher002',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'name' => 'Ana Dela Cruz',
                'email' => 'ana.student@example.com',
                'password' => $password,
                'role' => 'student',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'student001',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'name' => 'Ben Ramos',
                'email' => 'ben.student@example.com',
                'password' => $password,
                'role' => 'student',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'student002',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'name' => 'Cara Mendoza',
                'email' => 'cara.student@example.com',
                'password' => $password,
                'role' => 'student',
                'email_verified_at' => $this->seededAt,
                'remember_token' => 'student003',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
        ];

        DB::table('users')->upsert(
            $rows,
            ['email'],
            ['name', 'password', 'role', 'email_verified_at', 'remember_token', 'updated_at']
        );

        return $this->fetchKeyedRecords('users', 'email', array_column($rows, 'email'));
    }

    /**
     * @return array<int|string, object>
     */
    private function seedAcademicTerms(): array
    {
        $rows = [
            [
                'school_year' => '2025-2026',
                'semester' => '2nd Semester',
                'start_date' => '2026-01-12',
                'end_date' => '2026-05-30',
                'is_active' => true,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'school_year' => '2024-2025',
                'semester' => '1st Semester',
                'start_date' => '2024-08-12',
                'end_date' => '2024-12-20',
                'is_active' => false,
                'created_at' => $this->seededAt->subYear(),
                'updated_at' => $this->seededAt,
            ],
        ];

        DB::table('academic_terms')->upsert(
            $rows,
            ['school_year', 'semester'],
            ['start_date', 'end_date', 'is_active', 'updated_at']
        );

        return DB::table('academic_terms')
            ->whereIn('school_year', ['2025-2026', '2024-2025'])
            ->whereIn('semester', ['2nd Semester', '1st Semester'])
            ->get()
            ->keyBy(fn (object $term): string => $term->school_year.'|'.$term->semester)
            ->all();
    }

    /**
     * @return array<int|string, object>
     */
    private function seedPrograms(): array
    {
        $rows = [
            [
                'external_id' => 101,
                'custom_id' => 'PRG000101',
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'department' => 'CCS',
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 102,
                'custom_id' => 'PRG000102',
                'code' => 'BSCS',
                'name' => 'Bachelor of Science in Computer Science',
                'department' => 'CCS',
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 103,
                'custom_id' => 'PRG000103',
                'code' => 'BSA',
                'name' => 'Bachelor of Science in Accountancy',
                'department' => 'CBAA',
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
        ];

        foreach ($rows as $row) {
            $existingProgram = DB::table('programs')
                ->where('external_id', $row['external_id'])
                ->orWhere(function ($query) use ($row): void {
                    $query->where('name', $row['name'])
                        ->where('department', $row['department']);
                })
                ->first();

            if ($existingProgram !== null) {
                DB::table('programs')
                    ->where('id', $existingProgram->id)
                    ->update([
                        'external_id' => $row['external_id'],
                        'custom_id' => $row['custom_id'],
                        'code' => $row['code'],
                        'name' => $row['name'],
                        'department' => $row['department'],
                        'status' => $row['status'],
                        'updated_at' => $this->seededAt,
                    ]);

                continue;
            }

            DB::table('programs')->insert($row);
        }

        return $this->fetchKeyedRecords('programs', 'code', ['BSIT', 'BSCS', 'BSA']);
    }

    /**
     * @param  array<int|string, object>  $programs
     * @return array<int|string, object>
     */
    private function seedSubjects(array $programs): array
    {
        $rows = [
            [
                'external_id' => 201,
                'custom_id' => 'SUB000201',
                'code' => 'IT101',
                'subject_code' => 'IT101',
                'name' => 'Introduction to Computing',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => $programs['BSIT']->id,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 202,
                'custom_id' => 'SUB000202',
                'code' => 'IT102',
                'subject_code' => 'IT102',
                'name' => 'Computer Programming 1',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => $programs['BSIT']->id,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 203,
                'custom_id' => 'SUB000203',
                'code' => 'ITL103',
                'subject_code' => 'ITL103',
                'name' => 'Programming 1 Laboratory',
                'units' => $this->decimal(1),
                'type' => 'lab',
                'status' => 'active',
                'program_id' => $programs['BSIT']->id,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 204,
                'custom_id' => 'SUB000204',
                'code' => 'CS201',
                'subject_code' => 'CS201',
                'name' => 'Data Structures and Algorithms',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => $programs['BSCS']->id,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 205,
                'custom_id' => 'SUB000205',
                'code' => 'ACC101',
                'subject_code' => 'ACC101',
                'name' => 'Fundamentals of Accounting',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => $programs['BSA']->id,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 206,
                'custom_id' => 'SUB000206',
                'code' => 'GE101',
                'subject_code' => 'GE101',
                'name' => 'Understanding the Self',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => null,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'external_id' => 207,
                'custom_id' => 'SUB000207',
                'code' => 'NSTP1',
                'subject_code' => 'NSTP1',
                'name' => 'National Service Training Program 1',
                'units' => $this->decimal(3),
                'type' => 'lecture',
                'status' => 'active',
                'program_id' => null,
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
        ];

        foreach ($rows as $row) {
            $existingSubject = DB::table('subjects')
                ->where('external_id', $row['external_id'])
                ->orWhere('code', $row['code'])
                ->first();

            if ($existingSubject !== null) {
                DB::table('subjects')
                    ->where('id', $existingSubject->id)
                    ->update([
                        'external_id' => $row['external_id'],
                        'custom_id' => $row['custom_id'],
                        'code' => $row['code'],
                        'subject_code' => $row['subject_code'],
                        'name' => $row['name'],
                        'units' => $row['units'],
                        'type' => $row['type'],
                        'status' => $row['status'],
                        'program_id' => $row['program_id'],
                        'updated_at' => $this->seededAt,
                    ]);

                continue;
            }

            DB::table('subjects')->insert($row);
        }

        return $this->fetchKeyedRecords(
            'subjects',
            'code',
            ['IT101', 'IT102', 'ITL103', 'CS201', 'ACC101', 'GE101', 'NSTP1']
        );
    }

    /**
     * @return array<int|string, object>
     */
    private function seedPaymentMethods(): array
    {
        $rows = [
            ['code' => 'CASH', 'name' => 'Cash', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['code' => 'CARD', 'name' => 'Credit/Debit Card', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['code' => 'CHECK', 'name' => 'Check', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['code' => 'BANK_TRANSFER', 'name' => 'Bank Transfer', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['code' => 'ONLINE', 'name' => 'Online Payment', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['code' => 'OTHER', 'name' => 'Other', 'is_active' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
        ];

        DB::table('payment_methods')->upsert(
            $rows,
            ['code'],
            ['name', 'is_active', 'updated_at']
        );

        return $this->fetchKeyedRecords(
            'payment_methods',
            'code',
            ['CASH', 'CARD', 'CHECK', 'BANK_TRANSFER', 'ONLINE', 'OTHER']
        );
    }

    /**
     * @param  array<int|string, object>  $programs
     * @param  array<int|string, object>  $subjects
     */
    private function seedProgramSubject(array $programs, array $subjects): void
    {
        $rows = [
            ['program_id' => $programs['BSIT']->id, 'subject_id' => $subjects['IT101']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'subject_id' => $subjects['IT102']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'subject_id' => $subjects['ITL103']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'subject_id' => $subjects['GE101']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'subject_id' => $subjects['NSTP1']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'subject_id' => $subjects['CS201']->id, 'year_level' => '2', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'subject_id' => $subjects['GE101']->id, 'year_level' => '2', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'subject_id' => $subjects['NSTP1']->id, 'year_level' => '2', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'subject_id' => $subjects['ACC101']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'subject_id' => $subjects['GE101']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'subject_id' => $subjects['NSTP1']->id, 'year_level' => '1', 'semester' => '2nd Semester', 'school_year' => '2025-2026', 'status' => 'active', 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
        ];

        DB::table('program_subject')->upsert(
            $rows,
            ['program_id', 'subject_id'],
            ['year_level', 'semester', 'school_year', 'status', 'updated_at']
        );
    }

    /**
     * @param  array<int|string, object>  $users
     */
    private function seedTeachers(array $users): void
    {
        $rows = [
            [
                'user_id' => $users['maria.teacher@example.com']->id,
                'teacher_id' => 'T-2026-001',
                'first_name' => 'Maria',
                'middle_name' => 'L.',
                'last_name' => 'Santos',
                'department' => 'CCS',
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'user_id' => $users['jose.teacher@example.com']->id,
                'teacher_id' => 'T-2026-002',
                'first_name' => 'Jose',
                'middle_name' => 'R.',
                'last_name' => 'Reyes',
                'department' => 'CBAA',
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
        ];

        DB::table('teachers')->upsert(
            $rows,
            ['teacher_id'],
            ['user_id', 'first_name', 'middle_name', 'last_name', 'department', 'status', 'updated_at']
        );
    }

    /**
     * @param  array<int|string, object>  $users
     * @param  array<int|string, object>  $programs
     * @return array<int|string, object>
     */
    private function seedStudents(array $users, array $programs): array
    {
        $rows = [
            [
                'user_id' => $users['ana.student@example.com']->id,
                'student_no' => '2026-0001',
                'first_name' => 'Ana',
                'middle_name' => 'M.',
                'last_name' => 'Dela Cruz',
                'program_id' => $programs['BSIT']->id,
                'year_level' => 1,
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'user_id' => $users['ben.student@example.com']->id,
                'student_no' => '2026-0002',
                'first_name' => 'Ben',
                'middle_name' => 'T.',
                'last_name' => 'Ramos',
                'program_id' => $programs['BSCS']->id,
                'year_level' => 2,
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
            [
                'user_id' => $users['cara.student@example.com']->id,
                'student_no' => '2026-0003',
                'first_name' => 'Cara',
                'middle_name' => null,
                'last_name' => 'Mendoza',
                'program_id' => $programs['BSA']->id,
                'year_level' => 1,
                'status' => 'active',
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ],
        ];

        DB::table('students')->upsert(
            $rows,
            ['student_no'],
            ['user_id', 'first_name', 'middle_name', 'last_name', 'program_id', 'year_level', 'status', 'updated_at']
        );

        return $this->fetchKeyedRecords('students', 'student_no', ['2026-0001', '2026-0002', '2026-0003']);
    }

    /**
     * @param  array<int|string, object>  $programs
     * @return array<int|string, object>
     */
    private function seedFeeStructures(array $programs): array
    {
        $rows = [
            ['program_id' => $programs['BSIT']->id, 'fee_type' => 'tuition', 'amount' => $this->decimal(1450), 'per_unit' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'fee_type' => 'miscellaneous', 'amount' => $this->decimal(2500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'fee_type' => 'lab_fee', 'amount' => $this->decimal(1200), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'fee_type' => 'registration', 'amount' => $this->decimal(750), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSIT']->id, 'fee_type' => 'student activities', 'amount' => $this->decimal(500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'fee_type' => 'tuition', 'amount' => $this->decimal(1500), 'per_unit' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'fee_type' => 'miscellaneous', 'amount' => $this->decimal(2500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'fee_type' => 'lab_fee', 'amount' => $this->decimal(1200), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'fee_type' => 'registration', 'amount' => $this->decimal(750), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSCS']->id, 'fee_type' => 'student activities', 'amount' => $this->decimal(500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'fee_type' => 'tuition', 'amount' => $this->decimal(1350), 'per_unit' => true, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'fee_type' => 'miscellaneous', 'amount' => $this->decimal(2500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'fee_type' => 'lab_fee', 'amount' => $this->decimal(1200), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'fee_type' => 'registration', 'amount' => $this->decimal(750), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
            ['program_id' => $programs['BSA']->id, 'fee_type' => 'student activities', 'amount' => $this->decimal(500), 'per_unit' => false, 'created_at' => $this->seededAt, 'updated_at' => $this->seededAt],
        ];

        DB::table('fee_structure')->upsert(
            $rows,
            ['program_id', 'fee_type'],
            ['amount', 'per_unit', 'updated_at']
        );

        $records = DB::table('fee_structure')
            ->whereIn('program_id', [$programs['BSIT']->id, $programs['BSCS']->id, $programs['BSA']->id])
            ->get();

        $keyed = [];
        foreach ($records as $record) {
            $keyed[$record->program_id.'|'.$record->fee_type] = $record;
        }

        return $keyed;
    }

    /**
     * @param  array<int|string, object>  $students
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $terms
     */
    private function seedEnrollments(array $students, array $subjects, array $terms): void
    {
        $activeTermId = $terms[$this->activeTermKey()]->id;
        $historicalTermId = $terms[$this->historicalTermKey()]->id;

        $rows = [];
        foreach ($this->currentTermSubjectsByStudent() as $studentNo => $subjectCodes) {
            foreach ($subjectCodes as $subjectCode) {
                $rows[] = [
                    'student_id' => $students[$studentNo]->id,
                    'subject_id' => $subjects[$subjectCode]->id,
                    'academic_term_id' => $activeTermId,
                    'status' => 'enrolled',
                    'created_at' => $this->seededAt,
                    'updated_at' => $this->seededAt,
                ];
            }
        }

        $rows[] = [
            'student_id' => $students['2026-0002']->id,
            'subject_id' => $subjects['IT102']->id,
            'academic_term_id' => $historicalTermId,
            'status' => 'dropped',
            'created_at' => $this->seededAt->subMonths(12),
            'updated_at' => $this->seededAt,
        ];

        DB::table('enrollments')->upsert(
            $rows,
            ['student_id', 'subject_id', 'academic_term_id'],
            ['status', 'updated_at']
        );
    }

    /**
     * @param  array<int|string, object>  $students
     * @param  array<int|string, object>  $terms
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $feeStructures
     * @return array<int|string, object>
     */
    private function seedAssessments(array $students, array $terms, array $subjects, array $feeStructures): array
    {
        $activeTerm = $terms[$this->activeTermKey()];

        foreach ($this->currentTermSubjectsByStudent() as $studentNo => $subjectCodes) {
            $summary = $this->buildChargeSummary($students[$studentNo], $subjectCodes, $subjects, $feeStructures);

            $this->updateOrInsertTimestamped(
                'assessments',
                [
                    'student_id' => $students[$studentNo]->id,
                    'academic_term_id' => $activeTerm->id,
                ],
                [
                    'total_units' => $summary['total_units'],
                    'status' => 'finalized',
                ]
            );
        }

        $historicalSummary = $this->buildChargeSummary(
            $students['2026-0002'],
            ['IT102'],
            $subjects,
            $feeStructures
        );

        $this->updateOrInsertTimestamped(
            'assessments',
            [
                'student_id' => $students['2026-0002']->id,
                'academic_term_id' => $terms[$this->historicalTermKey()]->id,
            ],
            [
                'total_units' => $historicalSummary['total_units'],
                'status' => 'draft',
            ]
        );

        $records = [];
        foreach (array_keys($this->currentTermSubjectsByStudent()) as $studentNo) {
            $records[$studentNo] = DB::table('assessments')
                ->where('student_id', $students[$studentNo]->id)
                ->where('academic_term_id', $activeTerm->id)
                ->first();
        }

        return $records;
    }

    /**
     * @param  array<int|string, object>  $assessments
     * @param  array<int|string, object>  $students
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $feeStructures
     */
    private function seedAssessmentBreakdowns(array $assessments, array $students, array $subjects, array $feeStructures): void
    {
        foreach ($this->currentTermSubjectsByStudent() as $studentNo => $subjectCodes) {
            $summary = $this->buildChargeSummary($students[$studentNo], $subjectCodes, $subjects, $feeStructures);
            $assessment = $assessments[$studentNo];

            foreach ($summary['tuition_rows'] as $tuitionRow) {
                $this->updateOrInsertTimestamped(
                    'assessment_breakdown',
                    [
                        'assessment_id' => $assessment->id,
                        'source_type' => 'subject',
                        'description' => $tuitionRow['description'],
                    ],
                    [
                        'source_id' => $tuitionRow['subject_id'],
                        'units' => $tuitionRow['quantity'],
                        'rate' => $tuitionRow['unit_price'],
                        'amount' => $tuitionRow['amount'],
                    ]
                );
            }

            foreach ($summary['fee_rows'] as $feeRow) {
                $this->updateOrInsertTimestamped(
                    'assessment_breakdown',
                    [
                        'assessment_id' => $assessment->id,
                        'source_type' => 'fee',
                        'description' => $feeRow['description'],
                    ],
                    [
                        'source_id' => $feeRow['source_id'],
                        'units' => $feeRow['quantity'],
                        'rate' => $feeRow['unit_price'],
                        'amount' => $feeRow['amount'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array<int|string, object>  $assessments
     * @param  array<int|string, object>  $students
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $feeStructures
     * @return array<int|string, object>
     */
    private function seedInvoices(array $assessments, array $students, array $subjects, array $feeStructures): array
    {
        $rows = [];

        foreach ($this->invoiceBlueprints() as $studentNo => $blueprint) {
            $summary = $this->buildChargeSummary($students[$studentNo], $this->currentTermSubjectsByStudent()[$studentNo], $subjects, $feeStructures);
            $paid = $this->sumBlueprintPayments($studentNo);
            $total = (float) $summary['total_amount'];
            $balance = max(0, $total - $paid);

            $rows[] = [
                'student_id' => $students[$studentNo]->id,
                'assessment_id' => $assessments[$studentNo]->id,
                'invoice_number' => $blueprint['invoice_number'],
                'total_amount' => $summary['total_amount'],
                'balance' => $this->decimal($balance),
                'due_date' => $blueprint['due_date'],
                'status' => $this->resolveInvoiceStatus($balance, $total, $blueprint['due_date']),
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ];
        }

        DB::table('invoices')->upsert(
            $rows,
            ['invoice_number'],
            ['student_id', 'assessment_id', 'total_amount', 'balance', 'due_date', 'status', 'updated_at']
        );

        return $this->fetchKeyedRecords(
            'invoices',
            'invoice_number',
            array_column($rows, 'invoice_number')
        );
    }

    /**
     * @param  array<int|string, object>  $invoices
     * @param  array<int|string, object>  $students
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $feeStructures
     */
    private function seedInvoiceLines(array $invoices, array $students, array $subjects, array $feeStructures): void
    {
        foreach ($this->invoiceBlueprints() as $studentNo => $blueprint) {
            $summary = $this->buildChargeSummary($students[$studentNo], $this->currentTermSubjectsByStudent()[$studentNo], $subjects, $feeStructures);
            $invoice = $invoices[$blueprint['invoice_number']];

            foreach ($summary['tuition_rows'] as $tuitionRow) {
                $this->updateOrInsertTimestamped(
                    'invoice_lines',
                    [
                        'invoice_id' => $invoice->id,
                        'line_type' => 'tuition',
                        'description' => $tuitionRow['description'],
                    ],
                    [
                        'subject_id' => $tuitionRow['subject_id'],
                        'quantity' => $tuitionRow['quantity'],
                        'unit_price' => $tuitionRow['unit_price'],
                        'amount' => $tuitionRow['amount'],
                    ]
                );
            }

            foreach ($summary['fee_rows'] as $feeRow) {
                $this->updateOrInsertTimestamped(
                    'invoice_lines',
                    [
                        'invoice_id' => $invoice->id,
                        'line_type' => $feeRow['line_type'],
                        'description' => $feeRow['description'],
                    ],
                    [
                        'subject_id' => null,
                        'quantity' => $feeRow['quantity'],
                        'unit_price' => $feeRow['unit_price'],
                        'amount' => $feeRow['amount'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array<int|string, object>  $invoices
     * @param  array<int|string, object>  $paymentMethods
     * @return array<int|string, object>
     */
    private function seedPayments(array $invoices, array $paymentMethods): array
    {
        $rows = [];

        foreach ($this->paymentBlueprints() as $studentNo => $payments) {
            $invoiceNumber = $this->invoiceBlueprints()[$studentNo]['invoice_number'];

            foreach ($payments as $payment) {
                $rows[] = [
                    'invoice_id' => $invoices[$invoiceNumber]->id,
                    'amount_paid' => $this->decimal($payment['amount']),
                    'payment_method_id' => $paymentMethods[$payment['method_code']]->id,
                    'reference_number' => $payment['reference_number'],
                    'paid_at' => $payment['paid_at'],
                    'created_at' => $this->seededAt,
                    'updated_at' => $this->seededAt,
                ];
            }
        }

        foreach ($rows as $row) {
            $this->updateOrInsertTimestamped(
                'payments',
                ['reference_number' => $row['reference_number']],
                [
                    'invoice_id' => $row['invoice_id'],
                    'amount_paid' => $row['amount_paid'],
                    'payment_method_id' => $row['payment_method_id'],
                    'paid_at' => $row['paid_at'],
                ]
            );
        }

        return $this->fetchKeyedRecords(
            'payments',
            'reference_number',
            array_column($rows, 'reference_number')
        );
    }

    /**
     * @param  array<int|string, object>  $payments
     * @return array<int|string, object>
     */
    private function seedRefunds(array $payments): array
    {
        $this->updateOrInsertTimestamped(
            'refunds',
            [
                'payment_id' => $payments['ONLINE-DEMO-0003']->id,
                'reason' => 'Duplicate card charge detected',
            ],
            [
                'amount' => $this->decimal(500),
                'status' => 'approved',
            ]
        );

        $refund = DB::table('refunds')
            ->where('payment_id', $payments['ONLINE-DEMO-0003']->id)
            ->where('reason', 'Duplicate card charge detected')
            ->first();

        return [
            'ben-online-refund' => $refund,
        ];
    }

    /**
     * @param  array<int|string, object>  $payments
     * @param  array<int|string, object>  $invoices
     */
    private function seedPaymentAllocations(array $payments, array $invoices): void
    {
        $allocations = [
            ['reference_number' => 'CASH-DEMO-0001', 'invoice_number' => 'INV-DEMO-2026-0001', 'amount' => 23800],
            ['reference_number' => 'CARD-DEMO-0002', 'invoice_number' => 'INV-DEMO-2026-0002', 'amount' => 7000],
            ['reference_number' => 'ONLINE-DEMO-0003', 'invoice_number' => 'INV-DEMO-2026-0002', 'amount' => 3000],
        ];

        foreach ($allocations as $allocation) {
            $this->updateOrInsertTimestamped(
                'payment_allocations',
                [
                    'payment_id' => $payments[$allocation['reference_number']]->id,
                    'invoice_id' => $invoices[$allocation['invoice_number']]->id,
                ],
                [
                    'amount_applied' => $this->decimal($allocation['amount']),
                ]
            );
        }
    }

    /**
     * @param  array<int|string, object>  $payments
     */
    private function seedOfficialReceipts(array $payments): void
    {
        foreach ($this->paymentBlueprints() as $studentNo => $blueprints) {
            foreach ($blueprints as $blueprint) {
                $this->updateOrInsertTimestamped(
                    'official_receipts',
                    [
                        'payment_id' => $payments[$blueprint['reference_number']]->id,
                    ],
                    [
                        'or_number' => $blueprint['or_number'],
                        'issued_by' => $blueprint['issued_by'],
                        'issued_at' => $blueprint['paid_at'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array<int|string, object>  $invoices
     */
    private function syncInvoiceBalances(array $invoices): void
    {
        foreach ($invoices as $invoiceNumber => $invoice) {
            $paid = (float) DB::table('payments')
                ->where('invoice_id', $invoice->id)
                ->sum('amount_paid');

            $total = (float) $invoice->total_amount;
            $balance = max(0, $total - $paid);

            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update([
                    'balance' => $this->decimal($balance),
                    'status' => $this->resolveInvoiceStatus($balance, $total, $invoice->due_date),
                    'updated_at' => $this->seededAt,
                ]);
        }
    }

    /**
     * @param  array<int|string, object>  $users
     * @param  array<int|string, object>  $programs
     * @param  array<int|string, object>  $assessments
     * @param  array<int|string, object>  $invoices
     * @param  array<int|string, object>  $payments
     * @param  array<int|string, object>  $refunds
     */
    private function seedAuditLogs(
        array $users,
        array $programs,
        array $assessments,
        array $invoices,
        array $payments,
        array $refunds
    ): void {
        $entries = [
            [
                'user_id' => $users['admin@example.com']->id,
                'action' => 'seeded_program',
                'entity_type' => 'programs',
                'entity_id' => $programs['BSIT']->id,
                'ip_address' => '127.0.0.1',
            ],
            [
                'user_id' => $users['maria.teacher@example.com']->id,
                'action' => 'finalized_assessment',
                'entity_type' => 'assessments',
                'entity_id' => $assessments['2026-0001']->id,
                'ip_address' => '127.0.0.1',
            ],
            [
                'user_id' => $users['admin@example.com']->id,
                'action' => 'issued_invoice',
                'entity_type' => 'invoices',
                'entity_id' => $invoices['INV-DEMO-2026-0002']->id,
                'ip_address' => '127.0.0.1',
            ],
            [
                'user_id' => $users['admin@example.com']->id,
                'action' => 'recorded_payment',
                'entity_type' => 'payments',
                'entity_id' => $payments['CASH-DEMO-0001']->id,
                'ip_address' => '127.0.0.1',
            ],
            [
                'user_id' => $users['admin@example.com']->id,
                'action' => 'approved_refund',
                'entity_type' => 'refunds',
                'entity_id' => $refunds['ben-online-refund']->id,
                'ip_address' => '127.0.0.1',
            ],
        ];

        foreach ($entries as $entry) {
            $this->updateOrInsertTimestamped(
                'audit_logs',
                [
                    'user_id' => $entry['user_id'],
                    'action' => $entry['action'],
                    'entity_type' => $entry['entity_type'],
                    'entity_id' => $entry['entity_id'],
                ],
                [
                    'ip_address' => $entry['ip_address'],
                ]
            );
        }
    }

    /**
     * @param  array<int|string, object>  $users
     */
    private function seedSupportTables(array $users): void
    {
        $admin = $users['admin@example.com'];

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'ana.student@example.com'],
            [
                'token' => Hash::make('demo-reset-token'),
                'created_at' => $this->seededAt,
            ]
        );

        DB::table('personal_access_tokens')->updateOrInsert(
            ['token' => hash('sha256', 'demo-admin-token')],
            [
                'tokenable_type' => User::class,
                'tokenable_id' => $admin->id,
                'name' => 'demo-admin-token',
                'abilities' => '["*"]',
                'last_used_at' => $this->seededAt->subMinutes(5),
                'expires_at' => $this->seededAt->addMonth(),
                'created_at' => $this->seededAt,
                'updated_at' => $this->seededAt,
            ]
        );

        DB::table('sessions')->updateOrInsert(
            ['id' => 'demo-admin-session'],
            [
                'user_id' => $admin->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Demo Seeder Browser',
                'payload' => base64_encode(serialize([
                    '_token' => Str::random(40),
                    '_previous' => ['url' => '/dashboard'],
                ])),
                'last_activity' => $this->seededAt->timestamp,
            ]
        );

        DB::table('cache')->updateOrInsert(
            ['key' => 'demo:dashboard-summary'],
            [
                'value' => serialize([
                    'students' => 3,
                    'invoices' => 3,
                    'payments' => 3,
                ]),
                'expiration' => $this->seededAt->addHour()->timestamp,
            ]
        );

        DB::table('cache_locks')->updateOrInsert(
            ['key' => 'demo:sync-lock'],
            [
                'owner' => 'demo-seeder',
                'expiration' => $this->seededAt->addMinutes(15)->timestamp,
            ]
        );

        $jobPayload = json_encode([
            'displayName' => 'DemoSeedJob',
            'job' => 'Illuminate\Queue\CallQueuedHandler@call',
            'data' => [
                'commandName' => 'DemoSeedJob',
                'command' => 'O:8:"stdClass":0:{}',
            ],
        ]) ?: '{}';

        DB::table('jobs')->updateOrInsert(
            [
                'queue' => 'demo-seed',
                'payload' => $jobPayload,
            ],
            [
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => $this->seededAt->addYears(5)->timestamp,
                'created_at' => $this->seededAt->timestamp,
            ]
        );

        DB::table('job_batches')->upsert(
            [[
                'id' => 'demo-seed-batch',
                'name' => 'Demo Seed Batch',
                'total_jobs' => 1,
                'pending_jobs' => 0,
                'failed_jobs' => 1,
                'failed_job_ids' => '["11111111-1111-4111-8111-111111111111"]',
                'options' => '{}',
                'cancelled_at' => null,
                'created_at' => $this->seededAt->timestamp,
                'finished_at' => $this->seededAt->timestamp,
            ]],
            ['id'],
            ['name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids', 'options', 'cancelled_at', 'created_at', 'finished_at']
        );

        DB::table('failed_jobs')->upsert(
            [[
                'uuid' => '11111111-1111-4111-8111-111111111111',
                'connection' => 'database',
                'queue' => 'demo-seed',
                'payload' => $jobPayload,
                'exception' => 'Demo seeded failed job entry for queue visibility.',
                'failed_at' => $this->seededAt,
            ]],
            ['uuid'],
            ['connection', 'queue', 'payload', 'exception', 'failed_at']
        );
    }

    /**
     * @return array<string, list<string>>
     */
    private function currentTermSubjectsByStudent(): array
    {
        return [
            '2026-0001' => ['IT101', 'IT102', 'ITL103', 'GE101', 'NSTP1'],
            '2026-0002' => ['CS201', 'GE101', 'NSTP1'],
            '2026-0003' => ['ACC101', 'GE101', 'NSTP1'],
        ];
    }

    /**
     * @return array<string, array{invoice_number: string, due_date: string}>
     */
    private function invoiceBlueprints(): array
    {
        return [
            '2026-0001' => [
                'invoice_number' => 'INV-DEMO-2026-0001',
                'due_date' => $this->seededAt->addDays(10)->toDateString(),
            ],
            '2026-0002' => [
                'invoice_number' => 'INV-DEMO-2026-0002',
                'due_date' => $this->seededAt->addDays(7)->toDateString(),
            ],
            '2026-0003' => [
                'invoice_number' => 'INV-DEMO-2026-0003',
                'due_date' => $this->seededAt->subDays(10)->toDateString(),
            ],
        ];
    }

    /**
     * @return array<string, list<array{method_code: string, reference_number: string, amount: float, paid_at: string, or_number: string, issued_by: string}>>
     */
    private function paymentBlueprints(): array
    {
        return [
            '2026-0001' => [
                [
                    'method_code' => 'CASH',
                    'reference_number' => 'CASH-DEMO-0001',
                    'amount' => 23800.00,
                    'paid_at' => $this->seededAt->subDays(2)->toDateTimeString(),
                    'or_number' => 'OR-DEMO-0001',
                    'issued_by' => 'Cashier A',
                ],
            ],
            '2026-0002' => [
                [
                    'method_code' => 'CARD',
                    'reference_number' => 'CARD-DEMO-0002',
                    'amount' => 7000.00,
                    'paid_at' => $this->seededAt->subDay()->toDateTimeString(),
                    'or_number' => 'OR-DEMO-0002',
                    'issued_by' => 'Cashier B',
                ],
                [
                    'method_code' => 'ONLINE',
                    'reference_number' => 'ONLINE-DEMO-0003',
                    'amount' => 3000.00,
                    'paid_at' => $this->seededAt->subHours(6)->toDateTimeString(),
                    'or_number' => 'OR-DEMO-0003',
                    'issued_by' => 'Cashier B',
                ],
            ],
            '2026-0003' => [],
        ];
    }

    /**
     * @param  list<string>  $subjectCodes
     * @param  array<int|string, object>  $subjects
     * @param  array<int|string, object>  $feeStructures
     * @return array{
     *     total_units: string,
     *     total_amount: string,
     *     tuition_rows: list<array{
     *         subject_id: int,
     *         description: string,
     *         quantity: string,
     *         unit_price: string,
     *         amount: string
     *     }>,
     *     fee_rows: list<array{
     *         source_id: int|string,
     *         line_type: string,
     *         description: string,
     *         quantity: string|null,
     *         unit_price: string,
     *         amount: string
     *     }>
     * }
     */
    private function buildChargeSummary(object $student, array $subjectCodes, array $subjects, array $feeStructures): array
    {
        $tuitionFee = $feeStructures[$student->program_id.'|tuition'];
        $tuitionRate = (float) $tuitionFee->amount;
        $totalUnits = 0.0;
        $totalAmount = 0.0;
        $tuitionRows = [];

        foreach ($subjectCodes as $subjectCode) {
            $subject = $subjects[$subjectCode];
            $units = (float) $subject->units;
            $amount = $units * $tuitionRate;

            $tuitionRows[] = [
                'subject_id' => $subject->id,
                'description' => 'Tuition: '.$subject->code.' ('.$this->decimal($units).' units)',
                'quantity' => $this->decimal($units),
                'unit_price' => $this->decimal($tuitionRate),
                'amount' => $this->decimal($amount),
            ];

            $totalUnits += $units;
            $totalAmount += $amount;
        }

        $feeRows = [];
        foreach (['miscellaneous', 'lab_fee', 'registration', 'student activities'] as $feeType) {
            $fee = $feeStructures[$student->program_id.'|'.$feeType];
            $amount = $fee->per_unit
                ? $totalUnits * (float) $fee->amount
                : (float) $fee->amount;

            $feeRows[] = [
                'source_id' => $fee->id,
                'line_type' => $this->normalizeLineType($feeType),
                'description' => Str::headline($feeType),
                'quantity' => $fee->per_unit ? $this->decimal($totalUnits) : null,
                'unit_price' => $this->decimal((float) $fee->amount),
                'amount' => $this->decimal($amount),
            ];

            $totalAmount += $amount;
        }

        return [
            'total_units' => $this->decimal($totalUnits),
            'total_amount' => $this->decimal($totalAmount),
            'tuition_rows' => $tuitionRows,
            'fee_rows' => $feeRows,
        ];
    }

    private function normalizeLineType(string $feeType): string
    {
        return match ($feeType) {
            'lab_fee' => 'lab_fee',
            'miscellaneous' => 'misc_fee',
            default => 'other',
        };
    }

    private function activeTermKey(): string
    {
        return '2025-2026|2nd Semester';
    }

    private function historicalTermKey(): string
    {
        return '2024-2025|1st Semester';
    }

    private function sumBlueprintPayments(string $studentNo): float
    {
        $payments = $this->paymentBlueprints()[$studentNo] ?? [];

        return array_reduce(
            $payments,
            fn (float $carry, array $payment): float => $carry + $payment['amount'],
            0.0
        );
    }

    private function resolveInvoiceStatus(float $balance, float $total, string $dueDate): string
    {
        if ($balance <= 0) {
            return 'paid';
        }

        if (CarbonImmutable::parse($dueDate)->lt($this->seededAt)) {
            return 'overdue';
        }

        if ($balance < $total) {
            return 'partial';
        }

        return 'unpaid';
    }

    /**
     * @param  array<int|string>  $values
     * @return array<int|string, object>
     */
    private function fetchKeyedRecords(string $table, string $key, array $values): array
    {
        return DB::table($table)
            ->whereIn($key, $values)
            ->get()
            ->keyBy($key)
            ->all();
    }

    /**
     * @param  array<string, mixed>  $identity
     * @param  array<string, mixed>  $values
     */
    private function updateOrInsertTimestamped(string $table, array $identity, array $values): void
    {
        $exists = DB::table($table)->where($identity)->exists();

        DB::table($table)->updateOrInsert(
            $identity,
            array_merge(
                $values,
                ['updated_at' => $this->seededAt],
                $exists ? [] : ['created_at' => $this->seededAt]
            )
        );
    }

    private function decimal(float|int $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
