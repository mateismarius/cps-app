<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timesheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'project_id',
        'work_date',
        'clock_in',
        'clock_out',
        'hours_worked',
        'shift_type',
        'rate_id',
        'rate_amount',
        'rate_type',
        'notes',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours_worked' => 'decimal:2',
        'rate_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateAmount()
    {
        if ($this->rate_type === 'hourly') {
            return $this->hours_worked * $this->rate_amount;
        }

        return $this->rate_amount;
    }
}
