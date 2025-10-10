<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'model',
        'serial_number',
        'category',
        'description',
        'purchase_date',
        'purchase_price',
        'next_service_date',
        'next_calibration_date',
        'service_interval_days',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'next_service_date' => 'date',
        'next_calibration_date' => 'date',
    ];

    public function scopeNeedsService($query)
    {
        return $query->whereNotNull('next_service_date')
            ->where('next_service_date', '<=', now()->addDays(30));
    }
}
