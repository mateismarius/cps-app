<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'report_date',
        'shift_type',
        'submitted_by',
        'work_completed',
        'issues',
        'notes',
        'weather_conditions',
    ];

    protected $casts = [
        'report_date' => 'date',
        'weather_conditions' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
