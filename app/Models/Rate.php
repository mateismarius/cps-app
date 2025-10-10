<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'rateable_type',
        'rateable_id',
        'worker_id',
        'rate_type',
        'rate_amount',
        'currency',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'rate_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function rateable()
    {
        return $this->morphTo();
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    /**
     * Check if rate is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted rate display
     */
    public function getFormattedRateAttribute(): string
    {
        $amount = number_format($this->rate_amount, 2);
        $type = ucfirst($this->rate_type);

        return "£{$amount}/{$type}";
    }
}
