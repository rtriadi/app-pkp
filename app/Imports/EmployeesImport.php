<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\WorkUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];
    private $successCount = 0;
    private $errorCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Skip empty rows
            if (empty($row['nama']) || empty($row['nip'])) {
                $this->errors[] = 'Baris kosong atau data tidak lengkap dilewati';
                $this->errorCount++;
                return null;
            }

            // Validate work unit exists if provided
            $workUnitId = null;
            if (!empty($row['unit_kerja_id'])) {
                $workUnitId = (int) trim($row['unit_kerja_id']);
                $workUnit = WorkUnit::find($workUnitId);
                if (!$workUnit) {
                    $this->errors[] = 'Unit kerja dengan ID ' . $workUnitId . ' tidak ditemukan';
                    $this->errorCount++;
                    return null;
                }
            }

            $employee = new Employee([
                'nip' => trim($row['nip']),
                'name' => trim($row['nama']),
                'rank_grade' => !empty($row['pangkat_golongan']) ? trim($row['pangkat_golongan']) : null,
                'position' => !empty($row['jabatan']) ? trim($row['jabatan']) : null,
                'work_unit_id' => $workUnitId,
                'employee_status' => !empty($row['status']) ? trim($row['status']) : 'active',
                'hire_date' => !empty($row['tanggal_masuk']) ? date('Y-m-d', strtotime($row['tanggal_masuk'])) : null,
            ]);

            $this->successCount++;
            return $employee;
        } catch (Throwable $e) {
            Log::error('Employee import error: ' . $e->getMessage(), [
                'row' => $row,
                'error' => $e->getMessage()
            ]);

            $this->errors[] = 'Error pada baris: ' . json_encode($row) . ' - ' . $e->getMessage();
            $this->errorCount++;
            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nip' => 'required|string|max:20|unique:employees,nip',
            'nama' => 'required|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'unit_kerja_id' => 'nullable|integer|exists:work_units,id',
            'status' => 'nullable|in:active,inactive,retired',
            'tanggal_masuk' => 'nullable|date',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'status.in' => 'Status harus salah satu dari: active, inactive, retired',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
        ];
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = 'Validasi gagal pada baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            $this->errorCount++;
        }
    }

    /**
     * Handle import errors
     */
    public function onError(Throwable $e)
    {
        Log::error('Employee import error: ' . $e->getMessage());
        $this->errors[] = 'Error: ' . $e->getMessage();
        $this->errorCount++;
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
        ];
    }
}
