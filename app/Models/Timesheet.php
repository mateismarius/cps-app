<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'schedule_id',
        'engineer_id',
        'date',
        'approved',
    ];

    protected $casts = [
        'date' => 'date',
        'approved' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    // Based on schema: timesheets.engineer_id -> users.id
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // Helper to get Engineer model if needed
    public function engineerProfile(): BelongsTo
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'user_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForEngineer($query, $engineerId)
    {
        return $query->where('engineer_id', $engineerId);
    }
}
