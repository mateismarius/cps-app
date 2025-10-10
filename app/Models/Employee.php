<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'national_insurance_number',
        'address',
        'city',
        'postcode',
        'employment_start_date',
        'employment_end_date',
        'job_title',
        'department',
        'salary_amount',
        'salary_period',
        'holiday_allowance_days',
        'emergency_contact',
        'bank_details',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'employment_start_date' => 'date',
        'employment_end_date' => 'date',
        'salary_amount' => 'decimal:2',
        'emergency_contact' => 'array',
        'bank_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function worker()
    {
        return $this->hasOne(Worker::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($employee) {
            // Delete associated worker and its rates
            if ($employee->worker) {
                $employee->worker->rates()->delete();
                $employee->worker->delete();
            }

            // Delete leave requests
            $employee->leaveRequests()->delete();
        });
    }
}
