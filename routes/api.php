<?php

use App\Http\Controllers\AcademicTermsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AssessmentBreakdownController;
use App\Http\Controllers\AssessmentsController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DataSyncController;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\InvoiceLinesController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OfficialReceiptsController;
use App\Http\Controllers\PaymentAllocationsController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\RefundsController;
use App\Http\Controllers\SchollarshipController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TeachersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public endpoints (no auth required)
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// Authenticated endpoints (basic auth check)
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->prefix('user')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout');
});

// ============================================================================
// ADMIN ROUTES - Full access to all resources
// ============================================================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'stats']);

    Route::prefix('academic-terms')->controller(AcademicTermsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('assessment-breakdown')->controller(AssessmentBreakdownController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('assessments')->controller(AssessmentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('audit-logs')->controller(AuditLogsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('enrollments')->controller(EnrollmentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('fee-structures')->controller(FeeStructureController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('invoices')->controller(InvoicesController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('invoice-lines')->controller(InvoiceLinesController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('official-receipts')->controller(OfficialReceiptsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('payment-allocation')->controller(PaymentAllocationsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('payment-methods')->controller(PaymentMethodsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('payments')->controller(PaymentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('programs')->controller(ProgramsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('refunds')->controller(RefundsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('students')->controller(StudentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('scholarships')->controller(SchollarshipController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/students', 'studentsWithScholarships');
        Route::post('/', 'store');
        Route::post('/apply', 'applyScholarship');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('subjects')->controller(SubjectsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('teachers')->controller(TeachersController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Data Sync Routes (Admin Only)
    Route::prefix('sync')->controller(DataSyncController::class)->group(function () {
        Route::post('/', 'sync')->name('admin.sync');
        Route::get('/status', 'status')->name('admin.sync.status');
    });
});

// ============================================================================
// TEACHER ROUTES - Limited access to manage their subjects and assessments
// ============================================================================
Route::middleware(['auth:sanctum', 'teacher'])->prefix('teacher')->group(function () {

    Route::prefix('academic-terms')->controller(AcademicTermsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('subjects')->controller(SubjectsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('enrollments')->controller(EnrollmentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('assessments')->controller(AssessmentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('assessment-breakdown')->controller(AssessmentBreakdownController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'getTeacherProfile');
        Route::put('/', 'updateTeacherProfile');
    });
});

// ============================================================================
// STUDENT ROUTES - Access only to their own data
// ============================================================================
Route::middleware(['auth:sanctum', 'student'])->prefix('student')->group(function () {

    Route::prefix('enrollments')->controller(EnrollmentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('invoices')->controller(InvoicesController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('invoice-lines')->controller(InvoiceLinesController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('official-receipts')->controller(OfficialReceiptsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('payment-methods')->controller(PaymentMethodsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
    });

    Route::prefix('payments')->controller(PaymentsController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'getStudentProfile');
        Route::put('/', 'updateStudentProfile');
    });
});

Route::prefix('programs')->controller(ProfileController::class)->group(function () {
    Route::get('/', 'listPrograms');
});

Route::prefix('subjects')->controller(SubjectsController::class)->group(function () {
    Route::get('/', 'publicIndex');
});
