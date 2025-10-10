<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'subcontractor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Filament panel access
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Employee relationship (for user_type = 'employee')
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Subcontractor relationship (for user_type = 'subcontractor_ltd' or 'self_employed')
     */
    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }

    /**
     * Get worker through employee
     */
    public function workerThroughEmployee()
    {
        return $this->hasOneThrough(
            Worker::class,
            Employee::class,
            'user_id',
            'employee_id',
            'id',
            'id'
        );
    }

    /**
     * Get workers through subcontractor
     */
    public function workersThroughSubcontractor()
    {
        return $this->hasManyThrough(
            Worker::class,
            Subcontractor::class,
            'id',
            'subcontractor_id',
            'subcontractor_id',
            'id'
        );
    }

    /**
     * Get the primary worker for this user
     */
    public function getWorkerAttribute()
    {
        if ($this->user_type === 'employee') {
            return $this->employee?->worker;
        }

        if (in_array($this->user_type, ['subcontractor_ltd', 'self_employed'])) {
            return $this->subcontractor?->workers()->first();
        }

        return null;
    }

    /**
     * Projects managed by this user
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Projects supervised by this user
     */
    public function supervisedProjects()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee(): bool
    {
        return $this->user_type === 'employee';
    }

    /**
     * Check if user is a subcontractor
     */
    public function isSubcontractor(): bool
    {
        return in_array($this->user_type, ['subcontractor_ltd', 'self_employed']);
    }

    /**
     * Check if user has a worker profile
     */
    public function hasWorkerProfile(): bool
    {
        return $this->worker !== null;
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->employee) {
            return $this->employee->full_name;
        }

        if ($this->worker) {
            return $this->worker->full_name;
        }

        return $this->name;
    }

    /**
     * Get user's display type
     */
    public function getUserTypeDisplayAttribute(): string
    {
        return match($this->user_type) {
            'employee' => 'Employee',
            'self_employed' => 'Self Employed',
            'subcontractor_ltd' => 'Subcontractor (LTD)',
            default => 'Unknown',
        };
    }
}
