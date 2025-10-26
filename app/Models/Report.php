<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'timesheet_id',
        'engineer_id',
        'report_date',
        'summary',
        'file_path',
        'mime_type',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }

    // Based on schema: reports.engineer_id -> users.id
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // Helper to get Engineer model if needed
    public function engineerProfile(): BelongsTo
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'user_id');
    }

    // Scopes
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForEngineer($query, $engineerId)
    {
        return $query->where('engineer_id', $engineerId);
    }

    // File handling
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::exists($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->hasFile()) {
            return Storage::delete($this->file_path);
        }

        return false;
    }

    // Boot method to handle file deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($report) {
            $report->deleteFile();
        });
    }
}
