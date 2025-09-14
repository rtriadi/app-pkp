<?php

namespace App\Http\Controllers;

use App\Models\MonthlyAssessment;
use App\Models\AssessmentDetail;
use App\Models\PerformanceTarget;
use App\Models\PerformanceAgreement;
use App\Models\Document;
use App\Mail\MonthlyAssessmentSubmitted;
use App\Mail\MonthlyAssessmentApproved;
use App\Mail\DocumentUploaded;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MonthlyAssessmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MonthlyAssessment::with([
            'performanceAgreement:id,year,employee_id',
            'performanceAgreement.employee:id,name,nip,position',
            'assessor:id,name',
            'supervisor:id,name'
        ]);

        // Filter by agreement
        if ($request->has('agreement_id')) {
            $query->where('agreement_id', $request->agreement_id);
        }

        // Filter by month and year
        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Optimize ordering
        $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc');

        $assessments = $query->paginate(15)->withQueryString();

        return response()->json($assessments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agreement_id' => 'required|exists:performance_agreements,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'details' => 'required|array|min:1',
            'details.*.target_id' => 'required|exists:performance_targets,id',
            'details.*.ak_target' => 'required|numeric|min:0|max:100',
            'details.*.quantity_realization' => 'required|numeric|min:0',
            'details.*.quality_realization' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $assessment = MonthlyAssessment::create([
                'agreement_id' => $validated['agreement_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'status' => 'draft',
                'assessor_id' => Auth::id(),
                'supervisor_id' => Auth::id(), // TODO: Get actual supervisor
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalScore = 0;
            $detailCount = 0;

            foreach ($validated['details'] as $detailData) {
                $detail = AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'target_id' => $detailData['target_id'],
                    'ak_target' => $detailData['ak_target'],
                    'quantity_realization' => $detailData['quantity_realization'],
                    'quality_realization' => $detailData['quality_realization'],
                ]);

                $totalScore += $detail->score;
                $detailCount++;
            }

            $assessment->update([
                'total_score' => $detailCount > 0 ? $totalScore / $detailCount : 0,
            ]);

            DB::commit();
            return response()->json($assessment->load('assessmentDetails.performanceTarget'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create monthly assessment'], 500);
        }
    }

    public function show(MonthlyAssessment $assessment): JsonResponse
    {
        $assessment->load([
            'performanceAgreement.employee',
            'performanceAgreement.performanceTargets',
            'assessor',
            'supervisor',
            'assessmentDetails.performanceTarget'
        ]);

        return response()->json($assessment);
    }

    public function update(Request $request, MonthlyAssessment $assessment): JsonResponse
    {
        // Only allow updates if status is draft
        if ($assessment->status !== 'draft') {
            return response()->json(['error' => 'Cannot update submitted or approved assessments'], 403);
        }

        $validated = $request->validate([
            'details' => 'required|array|min:1',
            'details.*.target_id' => 'required|exists:performance_targets,id',
            'details.*.ak_target' => 'required|numeric|min:0|max:100',
            'details.*.quantity_realization' => 'required|numeric|min:0',
            'details.*.quality_realization' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing details
            $assessment->assessmentDetails()->delete();

            // Create new details
            $totalScore = 0;
            $detailCount = 0;

            foreach ($validated['details'] as $detailData) {
                $detail = AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'target_id' => $detailData['target_id'],
                    'ak_target' => $detailData['ak_target'],
                    'quantity_realization' => $detailData['quantity_realization'],
                    'quality_realization' => $detailData['quality_realization'],
                ]);

                $totalScore += $detail->score;
                $detailCount++;
            }

            $assessment->update([
                'total_score' => $detailCount > 0 ? $totalScore / $detailCount : 0,
                'notes' => $validated['notes'] ?? $assessment->notes,
            ]);

            DB::commit();
            return response()->json($assessment->load('assessmentDetails.performanceTarget'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update monthly assessment'], 500);
        }
    }

    public function submit(MonthlyAssessment $assessment): JsonResponse
    {
        if ($assessment->status !== 'draft') {
            return response()->json(['error' => 'Assessment is not in draft status'], 403);
        }

        $assessment->update(['status' => 'submitted']);

        // Send email notification to supervisor (atasan_pejabat)
        try {
            $supervisorUsers = \App\Models\User::where('role', 'atasan_pejabat')->get();
            foreach ($supervisorUsers as $supervisor) {
                Mail::to($supervisor->email)->send(new MonthlyAssessmentSubmitted($assessment));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send submission email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Assessment submitted for approval']);
    }

    public function approve(Request $request, MonthlyAssessment $assessment): JsonResponse
    {
        if ($assessment->status !== 'submitted') {
            return response()->json(['error' => 'Assessment is not submitted'], 403);
        }

        $assessment->update([
            'status' => 'approved',
            'supervisor_id' => Auth::id(),
        ]);

        // Send email notification to employee
        try {
            Mail::to($assessment->performanceAgreement->employee->user->email)->send(new MonthlyAssessmentApproved($assessment));
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Assessment approved']);
    }

    public function destroy(MonthlyAssessment $assessment): JsonResponse
    {
        if ($assessment->status !== 'draft') {
            return response()->json(['error' => 'Cannot delete submitted or approved assessments'], 403);
        }

        $assessment->delete();
        return response()->json(['message' => 'Assessment deleted']);
    }

    // Web Routes Methods
    public function webIndex(Request $request): Response
    {
        $query = MonthlyAssessment::with(['performanceAgreement.employee', 'assessor', 'supervisor']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month and year
        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        $assessments = $query->paginate(15);

        return Inertia::render('MonthlyAssessment/Index', [
            'assessments' => $assessments
        ]);
    }

    public function webCreate(): Response
    {
        $agreements = PerformanceAgreement::with(['employee', 'performanceTargets'])
            ->where('status', 'approved')
            ->orderBy('year', 'desc')
            ->get();

        return Inertia::render('MonthlyAssessment/Create', [
            'agreements' => $agreements
        ]);
    }

    public function webStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'agreement_id' => 'required|exists:performance_agreements,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'details' => 'required|array|min:1',
            'details.*.target_id' => 'required|exists:performance_targets,id',
            'details.*.ak_target' => 'required|numeric|min:0',
            'details.*.quantity_realization' => 'required|numeric|min:0',
            'details.*.quality_realization' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $assessment = MonthlyAssessment::create([
                'agreement_id' => $validated['agreement_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'status' => 'draft',
                'assessor_id' => Auth::id(),
                'supervisor_id' => Auth::id(), // TODO: Get actual supervisor
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalScore = 0;
            $detailCount = 0;

            foreach ($validated['details'] as $detailData) {
                $detail = AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'target_id' => $detailData['target_id'],
                    'ak_target' => $detailData['ak_target'],
                    'quantity_realization' => $detailData['quantity_realization'],
                    'quality_realization' => $detailData['quality_realization'],
                ]);

                $totalScore += $detail->score;
                $detailCount++;
            }

            $assessment->update([
                'total_score' => $detailCount > 0 ? $totalScore / $detailCount : 0,
            ]);

            DB::commit();
            return redirect()->route('monthly-assessments.show', $assessment->id)
                ->with('success', 'Penilaian bulanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat penilaian bulanan']);
        }
    }

    public function webShow(MonthlyAssessment $assessment): Response
    {
        $assessment->load([
            'performanceAgreement.employee.workUnit',
            'performanceAgreement.performanceTargets',
            'assessmentDetails.performanceTarget',
            'assessor',
            'supervisor'
        ]);

        return Inertia::render('MonthlyAssessment/Show', [
            'assessment' => $assessment
        ]);
    }

    public function webEdit(MonthlyAssessment $assessment): Response
    {
        if ($assessment->status !== 'draft') {
            return redirect()->route('monthly-assessments.show', $assessment->id)
                ->withErrors(['error' => 'Penilaian bulanan yang sudah diajukan tidak dapat diedit']);
        }

        $assessment->load([
            'performanceAgreement.employee',
            'performanceAgreement.performanceTargets',
            'assessmentDetails'
        ]);

        return Inertia::render('MonthlyAssessment/Edit', [
            'assessment' => $assessment
        ]);
    }

    public function webUpdate(Request $request, MonthlyAssessment $assessment): RedirectResponse
    {
        if ($assessment->status !== 'draft') {
            return back()->withErrors(['error' => 'Penilaian bulanan yang sudah diajukan tidak dapat diedit']);
        }

        $validated = $request->validate([
            'details' => 'required|array|min:1',
            'details.*.target_id' => 'required|exists:performance_targets,id',
            'details.*.ak_target' => 'required|numeric|min:0',
            'details.*.quantity_realization' => 'required|numeric|min:0',
            'details.*.quality_realization' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing details
            $assessment->assessmentDetails()->delete();

            // Create new details
            $totalScore = 0;
            $detailCount = 0;

            foreach ($validated['details'] as $detailData) {
                $detail = AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'target_id' => $detailData['target_id'],
                    'ak_target' => $detailData['ak_target'],
                    'quantity_realization' => $detailData['quantity_realization'],
                    'quality_realization' => $detailData['quality_realization'],
                ]);

                $totalScore += $detail->score;
                $detailCount++;
            }

            $assessment->update([
                'total_score' => $detailCount > 0 ? $totalScore / $detailCount : 0,
                'notes' => $validated['notes'] ?? $assessment->notes,
            ]);

            DB::commit();
            return redirect()->route('monthly-assessments.show', $assessment->id)
                ->with('success', 'Penilaian bulanan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui penilaian bulanan']);
        }
    }

    public function webDestroy(MonthlyAssessment $assessment): RedirectResponse
    {
        if ($assessment->status !== 'draft') {
            return back()->withErrors(['error' => 'Penilaian bulanan yang sudah diajukan tidak dapat dihapus']);
        }

        $assessment->delete();
        return redirect()->route('monthly-assessments.index')
            ->with('success', 'Penilaian bulanan berhasil dihapus');
    }

    /**
     * Upload documents for assessment
     */
    public function uploadDocuments(Request $request, MonthlyAssessment $assessment): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|max:5',
            'files.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max per file
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
        ]);

        $uploadedDocuments = [];

        foreach ($request->file('files') as $index => $file) {
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . uniqid() . '_' . $originalName;
            $path = $file->storeAs('documents', $filename, 'public');

            $document = $assessment->documents()->create([
                'original_name' => $originalName,
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'description' => $request->descriptions[$index] ?? null,
                'uploaded_by' => Auth::id(),
            ]);

            $uploadedDocuments[] = $document;

            // Send email notification to supervisors
            try {
                $supervisorUsers = \App\Models\User::where('role', 'atasan_pejabat')->get();
                foreach ($supervisorUsers as $supervisor) {
                    Mail::to($supervisor->email)->send(new DocumentUploaded($document));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send document upload email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => count($uploadedDocuments) . ' dokumen berhasil diupload',
            'documents' => $uploadedDocuments
        ], 201);
    }

    /**
     * Get documents for assessment
     */
    public function getDocuments(MonthlyAssessment $assessment): JsonResponse
    {
        $documents = $assessment->documents()->with('uploader')->get();

        return response()->json($documents);
    }

    /**
     * Delete document
     */
    public function deleteDocument(MonthlyAssessment $assessment, Document $document): JsonResponse
    {
        // Check if document belongs to the assessment
        if ($document->assessment_id !== $assessment->id) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Check if user can delete (owner or admin)
        if ($document->uploaded_by !== Auth::id() && Auth::user()->role !== 'super_admin') {
            return response()->json(['error' => 'Unauthorized to delete this document'], 403);
        }

        // Check if assessment is still editable
        if ($assessment->status !== 'draft') {
            return response()->json(['error' => 'Cannot delete documents from submitted assessments'], 403);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    /**
     * Download document
     */
    public function downloadDocument(MonthlyAssessment $assessment, Document $document)
    {
        // Check if document belongs to the assessment
        if ($document->assessment_id !== $assessment->id) {
            abort(404, 'Document not found');
        }

        if (!$document->fileExists()) {
            abort(404, 'File not found');
        }

        return response()->download(storage_path('app/public/' . $document->path), $document->original_name);
    }
}
