<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkUnit;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $employees = Employee::with('workUnit')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Employee/Index', [
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $workUnits = WorkUnit::orderBy('name')->get();

        return Inertia::render('Employee/Create', [
            'workUnits' => $workUnits
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:employees,nip',
            'name' => 'required|string|max:255',
            'rank_grade' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'employee_status' => 'required|in:active,inactive,retired',
            'hire_date' => 'nullable|date',
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Pegawai berhasil ditambahkan',
            'employee' => $employee->load('workUnit')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): Response
    {
        $employee->load(['workUnit', 'performanceAgreements.monthlyAssessments']);

        return Inertia::render('Employee/Show', [
            'employee' => $employee
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): Response
    {
        $workUnits = WorkUnit::orderBy('name')->get();

        return Inertia::render('Employee/Edit', [
            'employee' => $employee->load('workUnit'),
            'workUnits' => $workUnits
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:255',
            'rank_grade' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'employee_status' => 'required|in:active,inactive,retired',
            'hire_date' => 'nullable|date',
        ]);

        $employee->update($validated);

        return response()->json([
            'message' => 'Pegawai berhasil diperbarui',
            'employee' => $employee->load('workUnit')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        // Check if employee has performance agreements
        if ($employee->performanceAgreements()->count() > 0) {
            return response()->json([
                'error' => 'Tidak dapat menghapus pegawai yang memiliki data perjanjian kinerja'
            ], 422);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Pegawai berhasil dihapus'
        ]);
    }

    /**
     * Import employees from Excel file
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new EmployeesImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            return response()->json([
                'message' => 'Import selesai',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        // Check if Excel template exists
        $templatePath = storage_path('app/templates/employee_import_template.xlsx');

        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'template_import_pegawai.xlsx');
        }

        // Fallback to CSV if Excel template doesn't exist
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_pegawai.csv"',
        ];

        $template = "nip,nama,pangkat_golongan,jabatan,unit_kerja_id,status,tanggal_masuk\n";
        $template .= "198501012010011001,AHMAD RAHMAN,Pembina Tk. I / IV.b,Juru Sita,1,active,2010-01-01\n";
        $template .= "198502022010011002,SITI NURHALIZA,Pembina / IV.a,Panitera Pengganti,1,active,2010-01-02\n";
        $template .= "198503032010011003,BUDI SANTOSO,Pembina / IV.a,Panitera,2,active,2010-01-03\n";

        return response($template, 200, $headers);
    }
}
