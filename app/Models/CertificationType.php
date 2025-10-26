<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'validity_months',
        'active',
    ];

    protected $casts = [
        'validity_months' => 'integer',
        'active' => 'boolean',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
