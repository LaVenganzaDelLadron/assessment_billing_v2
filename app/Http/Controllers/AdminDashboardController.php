<?php

namespace App\Http\Controllers;

use App\Models\Assessments;
use App\Models\Enrollments;
use App\Models\Invoices;
use App\Models\OfficialReceipts;
use App\Models\Payments;
use App\Models\Programs;
use App\Models\Students;
use App\Models\Subjects;
use App\Models\Teachers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'students' => $this->countIfTableExists(Students::class, 'students'),
                'teachers' => $this->countIfTableExists(Teachers::class, 'teachers'),
                'programs' => $this->countIfTableExists(Programs::class, 'programs'),
                'subjects' => $this->countIfTableExists(Subjects::class, 'subjects'),
                'assessments' => $this->countIfTableExists(Assessments::class, 'assessments'),
                'enrollments' => $this->countIfTableExists(Enrollments::class, 'enrollments'),
                'invoices' => $this->countIfTableExists(Invoices::class, 'invoices'),
                'payments' => $this->countIfTableExists(Payments::class, 'payments'),
                'official_receipts' => $this->countIfTableExists(OfficialReceipts::class, 'official_receipts'),
            ],
            'message' => 'Dashboard statistics retrieved successfully.',
            'status' => 'success',
        ], 200);
    }

    private function countIfTableExists(string $model, string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return $model::count();
    }
}
