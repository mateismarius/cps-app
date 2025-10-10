<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certifiable_type',
        'certifiable_id',
        'name',
        'type',
        'certification_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function certifiable()
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cert) {
            if ($cert->expiry_date) {
                if ($cert->expiry_date < now()) {
                    $cert->status = 'expired';
                } elseif ($cert->expiry_date <= now()->addDays(30)) {
                    $cert->status = 'expiring_soon';
                } else {
                    $cert->status = 'valid';
                }
            }
        });
    }

    public function scopeExpiring($query)
    {
        return $query->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now());
    }
}
