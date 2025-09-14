<?php

namespace App\Http\Controllers;

use App\Models\PerformanceAgreement;
use App\Models\MonthlyAssessment;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function performanceAgreementPdf(PerformanceAgreement $agreement): Response
    {
        $agreement->load([
            'employee.workUnit',
            'performanceTargets',
            'createdBy',
            'approvedBy'
        ]);

        $data = [
            'agreement' => $agreement,
            'employee' => $agreement->employee,
            'targets' => $agreement->performanceTargets,
            'createdBy' => $agreement->createdBy,
            'approvedBy' => $agreement->approvedBy,
        ];

        $pdf = Pdf::loadView('pdf.performance-agreement', $data);
        $filename = 'perjanjian-kinerja-' . $agreement->employee->name . '-' . $agreement->year . '.pdf';

        return $pdf->download($filename);
    }

    public function monthlyAssessmentPdf(MonthlyAssessment $assessment): Response
    {
        $assessment->load([
            'performanceAgreement.employee.workUnit',
            'performanceAgreement.performanceTargets',
            'assessmentDetails.performanceTarget',
            'assessor',
            'supervisor'
        ]);

        $data = [
            'assessment' => $assessment,
            'agreement' => $assessment->performanceAgreement,
            'employee' => $assessment->performanceAgreement->employee,
            'details' => $assessment->assessmentDetails,
            'assessor' => $assessment->assessor,
            'supervisor' => $assessment->supervisor,
        ];

        $pdf = Pdf::loadView('pdf.monthly-assessment', $data);
        $filename = 'penilaian-bulanan-' . $assessment->performanceAgreement->employee->name . '-' . $assessment->month . '-' . $assessment->year . '.pdf';

        return $pdf->download($filename);
    }

    public function recapPdf($employeeId, $year): Response
    {
        $employee = Employee::with('workUnit')->findOrFail($employeeId);

        $assessments = MonthlyAssessment::with([
            'performanceAgreement',
            'assessmentDetails.performanceTarget',
            'assessor',
            'supervisor'
        ])
            ->whereHas('performanceAgreement', function ($query) use ($employeeId, $year) {
                $query->where('employee_id', $employeeId)
                    ->where('year', $year);
            })
            ->where('year', $year)
            ->orderBy('month')
            ->get();

        $data = [
            'employee' => $employee,
            'year' => $year,
            'assessments' => $assessments,
        ];

        $pdf = Pdf::loadView('pdf.recap-report', $data);
        $filename = 'rekap-penilaian-' . $employee->name . '-' . $year . '.pdf';

        return $pdf->download($filename);
    }

    // Web Routes Methods
    public function webIndex(): InertiaResponse
    {
        return Inertia::render('Reports/Index');
    }

    public function webPerformanceAgreement(): InertiaResponse
    {
        $agreements = PerformanceAgreement::with(['employee', 'performanceTargets'])
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Reports/PerformanceAgreement', [
            'agreements' => $agreements
        ]);
    }

    public function webMonthlyAssessment(): InertiaResponse
    {
        $assessments = MonthlyAssessment::with([
            'performanceAgreement.employee',
            'assessor',
            'supervisor'
        ])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return Inertia::render('Reports/MonthlyAssessment', [
            'assessments' => $assessments
        ]);
    }

    public function webRecap(): InertiaResponse
    {
        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'nip', 'position', 'work_unit_id']);

        return Inertia::render('Reports/Recap', [
            'employees' => $employees
        ]);
    }
}
