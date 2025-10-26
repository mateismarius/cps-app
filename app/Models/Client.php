<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'default_rate',
        'active',
    ];

    protected $casts = [
        'default_rate' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'main_company_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
