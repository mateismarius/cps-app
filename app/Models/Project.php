<?php
// app/Models/Project.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
use HasFactory, SoftDeletes;

protected $fillable = [
'project_number',
'name',
'description',
'client_id',
'project_manager_id',
'supervisor_id',
'location',
'start_date',
'end_date',
'deadline',
'budget',
'actual_cost',
'billing_type',
'allocated_shifts',
'required_permits',
'risks',
'meetings',
'status',
];

protected $casts = [
'start_date' => 'date',
'end_date' => 'date',
'deadline' => 'date',
'budget' => 'decimal:2',
'actual_cost' => 'decimal:2',
'required_permits' => 'array',
'risks' => 'array',
'meetings' => 'array',
];

public function client()
{
return $this->belongsTo(Client::class);
}

public function projectManager()
{
return $this->belongsTo(User::class, 'project_manager_id');
}

public function supervisor()
{
return $this->belongsTo(User::class, 'supervisor_id');
}

public function workers()
{
return $this->belongsToMany(Worker::class, 'project_workers')
->withPivot('role', 'assigned_date', 'removed_date')
->withTimestamps();
}

public function timesheets()
{
return $this->hasMany(Timesheet::class);
}

public function tasks()
{
return $this->hasMany(ProjectTask::class);
}

public function shiftReports()
{
return $this->hasMany(ShiftReport::class);
}

public function documents()
{
return $this->morphMany(Document::class, 'documentable');
}

public function schedules()
{
return $this->hasMany(Schedule::class);
}
}
