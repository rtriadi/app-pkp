<?php

namespace App\Http\Controllers;

use App\Models\PerformanceAgreement;
use App\Models\PerformanceTarget;
use App\Models\Employee;
use App\Mail\PerformanceAgreementApproved;
use App\Mail\PerformanceAgreementRejected;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceAgreementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PerformanceAgreement::with([
            'employee:id,name,nip,position',
            'createdBy:id,name',
            'approvedBy:id,name'
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Optimize ordering
        $query->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc');

        $agreements = $query->paginate(15)->withQueryString();

        return response()->json($agreements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'targets' => 'required|array|min:1',
            'targets.*.activity_goal' => 'required|string|max:500',
            'targets.*.performance_indicator' => 'required|string|max:500',
            'targets.*.quality_target' => 'required|numeric|min:0|max:100',
            'targets.*.quantity_target' => 'required|numeric|min:0',
            'targets.*.unit' => 'required|string|max:50',
            'targets.*.months' => 'required|array|min:1|max:12',
            'targets.*.months.*' => 'integer|between:1,12',
        ]);

        DB::beginTransaction();
        try {
            $agreement = PerformanceAgreement::create([
                'employee_id' => $validated['employee_id'],
                'year' => $validated['year'],
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['targets'] as $targetData) {
                $months = array_fill(1, 12, false);
                foreach ($targetData['months'] as $month) {
                    $months[$month] = true;
                }

                PerformanceTarget::create([
                    'agreement_id' => $agreement->id,
                    'activity_goal' => $targetData['activity_goal'],
                    'performance_indicator' => $targetData['performance_indicator'],
                    'quality_target' => $targetData['quality_target'],
                    'quantity_target' => $targetData['quantity_target'],
                    'unit' => $targetData['unit'],
                    'jan' => $months[1],
                    'feb' => $months[2],
                    'mar' => $months[3],
                    'apr' => $months[4],
                    'may' => $months[5],
                    'jun' => $months[6],
                    'jul' => $months[7],
                    'aug' => $months[8],
                    'sep' => $months[9],
                    'oct' => $months[10],
                    'nov' => $months[11],
                    'dec' => $months[12],
                ]);
            }

            DB::commit();
            return response()->json($agreement->load('performanceTargets'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create performance agreement'], 500);
        }
    }

    public function show(PerformanceAgreement $agreement): JsonResponse
    {
        $agreement->load(['employee', 'performanceTargets', 'createdBy', 'approvedBy', 'monthlyAssessments']);
        return response()->json($agreement);
    }

    public function update(Request $request, PerformanceAgreement $agreement): JsonResponse
    {
        // Only allow updates if status is draft
        if ($agreement->status !== 'draft') {
            return response()->json(['error' => 'Cannot update approved or rejected agreements'], 403);
        }

        $validated = $request->validate([
            'targets' => 'required|array|min:1',
            'targets.*.activity_goal' => 'required|string|max:500',
            'targets.*.performance_indicator' => 'required|string|max:500',
            'targets.*.quality_target' => 'required|numeric|min:0|max:100',
            'targets.*.quantity_target' => 'required|numeric|min:0',
            'targets.*.unit' => 'required|string|max:50',
            'targets.*.months' => 'required|array|min:1|max:12',
            'targets.*.months.*' => 'integer|between:1,12',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing targets
            $agreement->performanceTargets()->delete();

            // Create new targets
            foreach ($validated['targets'] as $targetData) {
                $months = array_fill(1, 12, false);
                foreach ($targetData['months'] as $month) {
                    $months[$month] = true;
                }

                PerformanceTarget::create([
                    'agreement_id' => $agreement->id,
                    'activity_goal' => $targetData['activity_goal'],
                    'performance_indicator' => $targetData['performance_indicator'],
                    'quality_target' => $targetData['quality_target'],
                    'quantity_target' => $targetData['quantity_target'],
                    'unit' => $targetData['unit'],
                    'jan' => $months[1],
                    'feb' => $months[2],
                    'mar' => $months[3],
                    'apr' => $months[4],
                    'may' => $months[5],
                    'jun' => $months[6],
                    'jul' => $months[7],
                    'aug' => $months[8],
                    'sep' => $months[9],
                    'oct' => $months[10],
                    'nov' => $months[11],
                    'dec' => $months[12],
                ]);
            }

            DB::commit();
            return response()->json($agreement->load('performanceTargets'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update performance agreement'], 500);
        }
    }

    public function submitForApproval(PerformanceAgreement $agreement): JsonResponse
    {
        if ($agreement->status !== 'draft') {
            return response()->json(['error' => 'Agreement is not in draft status'], 403);
        }

        $agreement->update(['status' => 'pending']);
        return response()->json(['message' => 'Agreement submitted for approval']);
    }

    public function approve(PerformanceAgreement $agreement): JsonResponse
    {
        if ($agreement->status !== 'pending') {
            return response()->json(['error' => 'Agreement is not pending approval'], 403);
        }

        // Check if user has permission to approve (only atasan_pejabat can approve)
        if (Auth::user()->role !== 'atasan_pejabat') {
            return response()->json(['error' => 'Only Atasan Pejabat Penilai can approve agreements'], 403);
        }

        $agreement->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send email notification to employee
        try {
            Mail::to($agreement->employee->user->email)->send(new PerformanceAgreementApproved($agreement));
        } catch (\Exception $e) {
            // Log email error but don't fail the approval
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Agreement approved successfully']);
    }

    public function reject(Request $request, PerformanceAgreement $agreement): JsonResponse
    {
        if ($agreement->status !== 'pending') {
            return response()->json(['error' => 'Agreement is not pending approval'], 403);
        }

        // Check if user has permission to reject (only atasan_pejabat can reject)
        if (Auth::user()->role !== 'atasan_pejabat') {
            return response()->json(['error' => 'Only Atasan Pejabat Penilai can reject agreements'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $agreement->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send email notification to employee
        try {
            Mail::to($agreement->employee->user->email)->send(new PerformanceAgreementRejected($agreement, $validated['reason']));
        } catch (\Exception $e) {
            // Log email error but don't fail the rejection
            Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Agreement rejected successfully']);
    }

    public function destroy(PerformanceAgreement $agreement): JsonResponse
    {
        if ($agreement->status !== 'draft') {
            return response()->json(['error' => 'Cannot delete approved or rejected agreements'], 403);
        }

        $agreement->delete();
        return response()->json(['message' => 'Agreement deleted']);
    }

    // Web Routes Methods
    public function webIndex(Request $request): Response
    {
        $user = Auth::user();
        $query = PerformanceAgreement::with(['employee', 'createdBy', 'approvedBy']);

        // If user is a regular employee, only show their own agreements
        if ($user->role === 'pegawai') {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                // If no employee record found, show empty results
                $query->where('id', 0);
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        $agreements = $query->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('PerformanceAgreement/Index', [
            'agreements' => $agreements,
            'userRole' => $user->role
        ]);
    }

    public function webCreate(): Response
    {
        $user = Auth::user();

        // If user is a regular employee (pegawai), they can only create agreements for themselves
        if ($user->role === 'pegawai') {
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return redirect()->route('dashboard')
                    ->withErrors(['error' => 'Data pegawai tidak ditemukan. Silakan hubungi administrator.']);
            }

            return Inertia::render('PerformanceAgreement/Create', [
                'employees' => collect([$employee])->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                        'nip' => $emp->nip,
                    ];
                }),
                'isEmployeeOnly' => true
            ]);
        }

        // For admins and assessors, show all active employees
        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'nip']);

        return Inertia::render('PerformanceAgreement/Create', [
            'employees' => $employees,
            'isEmployeeOnly' => false
        ]);
    }

    public function webStore(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // If user is a regular employee, they can only create agreements for themselves
        if ($user->role === 'pegawai') {
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return back()->withErrors(['error' => 'Data pegawai tidak ditemukan. Silakan hubungi administrator.']);
            }

            // Override the employee_id to ensure they can only create for themselves
            $request->merge(['employee_id' => $employee->id]);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'targets' => 'required|array|min:1',
            'targets.*.activity_goal' => 'required|string|max:500',
            'targets.*.performance_indicator' => 'required|string|max:500',
            'targets.*.quality_target' => 'required|numeric|min:0|max:100',
            'targets.*.quantity_target' => 'required|numeric|min:0',
            'targets.*.unit' => 'required|string|max:50',
            'targets.*.months' => 'required|array|min:1|max:12',
            'targets.*.months.*' => 'integer|between:1,12',
        ]);

        DB::beginTransaction();
        try {
            $agreement = PerformanceAgreement::create([
                'employee_id' => $validated['employee_id'],
                'year' => $validated['year'],
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['targets'] as $targetData) {
                $months = array_fill(1, 12, false);
                foreach ($targetData['months'] as $month) {
                    $months[$month] = true;
                }

                PerformanceTarget::create([
                    'agreement_id' => $agreement->id,
                    'activity_goal' => $targetData['activity_goal'],
                    'performance_indicator' => $targetData['performance_indicator'],
                    'quality_target' => $targetData['quality_target'],
                    'quantity_target' => $targetData['quantity_target'],
                    'unit' => $targetData['unit'],
                    'jan' => $months[1],
                    'feb' => $months[2],
                    'mar' => $months[3],
                    'apr' => $months[4],
                    'may' => $months[5],
                    'jun' => $months[6],
                    'jul' => $months[7],
                    'aug' => $months[8],
                    'sep' => $months[9],
                    'oct' => $months[10],
                    'nov' => $months[11],
                    'dec' => $months[12],
                ]);
            }

            DB::commit();
            return redirect()->route('performance-agreements.show', $agreement->id)
                ->with('success', 'Perjanjian kinerja berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat perjanjian kinerja']);
        }
    }

    public function webShow(PerformanceAgreement $agreement): Response
    {
        $agreement->load([
            'employee.workUnit',
            'performanceTargets',
            'createdBy',
            'approvedBy',
            'monthlyAssessments'
        ]);

        return Inertia::render('PerformanceAgreement/Show', [
            'agreement' => $agreement
        ]);
    }

    public function webEdit(PerformanceAgreement $agreement): Response
    {
        if ($agreement->status !== 'draft') {
            return redirect()->route('performance-agreements.show', $agreement->id)
                ->withErrors(['error' => 'Perjanjian kinerja yang sudah disetujui tidak dapat diedit']);
        }

        $agreement->load('performanceTargets');

        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'nip']);

        return Inertia::render('PerformanceAgreement/Edit', [
            'agreement' => $agreement,
            'employees' => $employees
        ]);
    }

    public function webUpdate(Request $request, PerformanceAgreement $agreement): RedirectResponse
    {
        if ($agreement->status !== 'draft') {
            return back()->withErrors(['error' => 'Perjanjian kinerja yang sudah disetujui tidak dapat diedit']);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'targets' => 'required|array|min:1',
            'targets.*.activity_goal' => 'required|string|max:500',
            'targets.*.performance_indicator' => 'required|string|max:500',
            'targets.*.quality_target' => 'required|numeric|min:0|max:100',
            'targets.*.quantity_target' => 'required|numeric|min:0',
            'targets.*.unit' => 'required|string|max:50',
            'targets.*.months' => 'required|array|min:1|max:12',
            'targets.*.months.*' => 'integer|between:1,12',
        ]);

        DB::beginTransaction();
        try {
            $agreement->update(['year' => $validated['year']]);

            // Delete existing targets
            $agreement->performanceTargets()->delete();

            // Create new targets
            foreach ($validated['targets'] as $targetData) {
                $months = array_fill(1, 12, false);
                foreach ($targetData['months'] as $month) {
                    $months[$month] = true;
                }

                PerformanceTarget::create([
                    'agreement_id' => $agreement->id,
                    'activity_goal' => $targetData['activity_goal'],
                    'performance_indicator' => $targetData['performance_indicator'],
                    'quality_target' => $targetData['quality_target'],
                    'quantity_target' => $targetData['quantity_target'],
                    'unit' => $targetData['unit'],
                    'jan' => $months[1],
                    'feb' => $months[2],
                    'mar' => $months[3],
                    'apr' => $months[4],
                    'may' => $months[5],
                    'jun' => $months[6],
                    'jul' => $months[7],
                    'aug' => $months[8],
                    'sep' => $months[9],
                    'oct' => $months[10],
                    'nov' => $months[11],
                    'dec' => $months[12],
                ]);
            }

            DB::commit();
            return redirect()->route('performance-agreements.show', $agreement->id)
                ->with('success', 'Perjanjian kinerja berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui perjanjian kinerja']);
        }
    }

    public function webDestroy(PerformanceAgreement $agreement): RedirectResponse
    {
        if ($agreement->status !== 'draft') {
            return back()->withErrors(['error' => 'Perjanjian kinerja yang sudah disetujui tidak dapat dihapus']);
        }

        $agreement->delete();
        return redirect()->route('performance-agreements.index')
            ->with('success', 'Perjanjian kinerja berhasil dihapus');
    }
}
