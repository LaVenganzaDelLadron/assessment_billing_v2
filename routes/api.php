<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AcademicTermsController;
use App\Http\Controllers\AssessmentBreakdownController;
use App\Http\Controlllers\AssessmentsController;
use App\Http\Controllers\AuditLogControlller;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OfficialReceiptController;
use App\Http\Controllers\PaymentAllocationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\RefundsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


Route::middleware(['auth:sanctum'])->prefix('user')->controller(AuthController::class)->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('academic-terms')->controller(App\Http\Controllers\AcademicTermsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });




});






