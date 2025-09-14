<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PerformanceAgreementController;
use App\Http\Controllers\MonthlyAssessmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Employee management - accessible by super_admin and pejabat_penilai
    Route::middleware('role:super_admin,pejabat_penilai')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('employees/download-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.download-template');
    });

    // Performance agreements - accessible by all roles
    Route::middleware('role:super_admin,pejabat_penilai,atasan_pejabat,pegawai')->group(function () {
        Route::get('performance-agreements', [PerformanceAgreementController::class, 'webIndex'])->name('performance-agreements.index');
        Route::get('performance-agreements/create', [PerformanceAgreementController::class, 'webCreate'])->name('performance-agreements.create');
        Route::post('performance-agreements', [PerformanceAgreementController::class, 'webStore'])->name('performance-agreements.store');
        Route::get('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'webShow'])->name('performance-agreements.show');
        Route::get('performance-agreements/{agreement}/edit', [PerformanceAgreementController::class, 'webEdit'])->name('performance-agreements.edit');
        Route::put('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'webUpdate'])->name('performance-agreements.update');
        Route::delete('performance-agreements/{agreement}', [PerformanceAgreementController::class, 'webDestroy'])->name('performance-agreements.destroy');
    });

    // Monthly assessments - accessible by all roles
    Route::middleware('role:super_admin,pejabat_penilai,atasan_pejabat,pegawai')->group(function () {
        Route::get('monthly-assessments', [MonthlyAssessmentController::class, 'webIndex'])->name('monthly-assessments.index');
        Route::get('monthly-assessments/create', [MonthlyAssessmentController::class, 'webCreate'])->name('monthly-assessments.create');
        Route::post('monthly-assessments', [MonthlyAssessmentController::class, 'webStore'])->name('monthly-assessments.store');
        Route::get('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'webShow'])->name('monthly-assessments.show');
        Route::get('monthly-assessments/{assessment}/edit', [MonthlyAssessmentController::class, 'webEdit'])->name('monthly-assessments.edit');
        Route::put('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'webUpdate'])->name('monthly-assessments.update');
        Route::delete('monthly-assessments/{assessment}', [MonthlyAssessmentController::class, 'webDestroy'])->name('monthly-assessments.destroy');
    });

    // Reports - accessible by all roles
    Route::middleware('role:super_admin,pejabat_penilai,atasan_pejabat,pegawai')->group(function () {
        Route::get('reports', [ReportController::class, 'webIndex'])->name('reports.index');
        Route::get('reports/performance-agreement', [ReportController::class, 'webPerformanceAgreement'])->name('reports.performance-agreement');
        Route::get('reports/monthly-assessment', [ReportController::class, 'webMonthlyAssessment'])->name('reports.monthly-assessment');
        Route::get('reports/recap', [ReportController::class, 'webRecap'])->name('reports.recap');
    });
});

require __DIR__ . '/auth.php';
