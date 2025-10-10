<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'subcontractor_id',
        'worker_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'trades',
        'status',
    ];

    protected $casts = [
        'trades' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_workers')
            ->withPivot('role', 'assigned_date', 'removed_date')
            ->withTimestamps();
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function certifications()
    {
        return $this->morphMany(Certification::class, 'certifiable');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user associated with this worker
     */
    public function user()
    {
        if ($this->employee_id) {
            return $this->employee->user ?? null;
        }

        if ($this->subcontractor_id) {
            return $this->subcontractor->users()->first();
        }

        return null;
    }

    /**
     * Get trades as formatted string
     */
    public function getTradesDisplayAttribute(): string
    {
        if (!$this->trades) {
            return 'None';
        }

        return collect($this->trades)
            ->map(fn ($trade) => ucwords(str_replace('_', ' ', $trade)))
            ->join(', ');
    }

    /**
     * Check if worker has specific trade
     */
    public function hasTrade(string $trade): bool
    {
        return in_array($trade, $this->trades ?? []);
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($worker) {
            // Delete all rates associated with this worker
            $worker->rates()->delete();

            // Delete schedules
            $worker->schedules()->delete();
        });
    }
}
