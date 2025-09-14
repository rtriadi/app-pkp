<?php

use App\Http\Controllers\PerformanceAgreementController;
use App\Http\Controllers\MonthlyAssessmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Performance Agreement Routes - accessible by all roles
Route::middleware(['auth:sanctum', 'role:super_admin,pejabat_penilai,atasan_pejabat,pegawai', 'cache.response:10'])->group(function () {
    Route::get('performance-agreements', [PerformanceAgreementController::class, 'index']);
    Route::get('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:super_admin,pejabat_penilai,atasan_pejabat,pegawai'])->group(function () {
    Route::post('performance-agreements', [PerformanceAgreementController::class, 'store']);
    Route::put('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'update']);
    Route::delete('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'destroy']);
    Route::post('performance-agreements/{agreement}/approve', [PerformanceAgreementController::class, 'approve']);
    Route::post('performance-agreements/{agreement}/reject', [PerformanceAgreementController::class, 'reject']);
    Route::post('performance-agreements/{agreement}/submit', [PerformanceAgreementController::class, 'submitForApproval']);
});

// Monthly Assessment Routes - accessible by all roles
Route::middleware(['auth:sanctum', 'role:super_admin,pejabat_penilai,atasan_pejabat,pegawai', 'cache.response:10'])->group(function () {
    Route::get('monthly-assessments', [MonthlyAssessmentController::class, 'index']);
    Route::get('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'show']);
    Route::get('monthly-assessments/{assessment}/documents', [MonthlyAssessmentController::class, 'getDocuments']);
});

Route::middleware(['auth:sanctum', 'role:super_admin,pejabat_penilai,atasan_pejabat,pegawai'])->group(function () {
    Route::post('monthly-assessments', [MonthlyAssessmentController::class, 'store']);
    Route::put('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'update']);
    Route::delete('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'destroy']);
    Route::post('monthly-assessments/{assessment}/submit', [MonthlyAssessmentController::class, 'submit']);
    Route::post('monthly-assessments/{assessment}/approve', [MonthlyAssessmentController::class, 'approve']);

    // Document upload routes
    Route::post('monthly-assessments/{assessment}/documents', [MonthlyAssessmentController::class, 'uploadDocuments']);
    Route::delete('monthly-assessments/{assessment}/documents/{document}', [MonthlyAssessmentController::class, 'deleteDocument']);
    Route::get('monthly-assessments/{assessment}/documents/{document}/download', [MonthlyAssessmentController::class, 'downloadDocument']);
});

// Report Routes - accessible by all roles
Route::middleware(['auth:sanctum', 'role:super_admin,pejabat_penilai,atasan_pejabat,pegawai'])->group(function () {
    Route::get('reports/performance-agreement/{agreement}/pdf', [ReportController::class, 'performanceAgreementPdf']);
    Route::get('reports/monthly-assessment/{assessment}/pdf', [ReportController::class, 'monthlyAssessmentPdf']);
    Route::get('reports/recap/{employee}/{year}/pdf', [ReportController::class, 'recapPdf']);
});
