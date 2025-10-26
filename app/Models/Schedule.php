<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'engineer_id',
        'date',
        'location',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Based on schema: schedules.engineer_id -> users.id
    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // Helper to get Engineer model if needed
    public function engineerProfile(): BelongsTo
    {
        return $this->belongsTo(Engineer::class, 'engineer_id', 'user_id');
    }

    // Scope for filtering by date range
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Scope for filtering by project
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }


    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }
}
