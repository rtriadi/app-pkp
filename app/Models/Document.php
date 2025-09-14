<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'assessment_id',
        'original_name',
        'filename',
        'path',
        'mime_type',
        'size',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function monthlyAssessment(): BelongsTo
    {
        return $this->belongsTo(MonthlyAssessment::class, 'assessment_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file size in human readable format
     */
    public function getSizeInKbAttribute(): string
    {
        return number_format($this->size / 1024, 2) . ' KB';
    }

    /**
     * Get the file size in human readable format
     */
    public function getSizeInMbAttribute(): string
    {
        return number_format($this->size / 1024 / 1024, 2) . ' MB';
    }

    /**
     * Get the file URL
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return Storage::exists($this->path);
    }

    /**
     * Delete the file from storage
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::delete($this->path);
        }
        return true;
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    protected static function booted()
    {
        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
