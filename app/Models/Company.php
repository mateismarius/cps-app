<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_company_id',
        'name',
        'type',
        'contact_person',
        'email',
        'phone',
        'address',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Company types
    public const TYPE_MAIN = 'main';
    public const TYPE_LTD = 'ltd';
    public const TYPE_SELF_EMPLOYED = 'self-employed';

    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function subCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    public function engineers(): HasMany
    {
        return $this->hasMany(Engineer::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'main_company_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'main_company_id');
    }

    public function issuedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'issuer_company_id');
    }

    public function receivedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'receiver_company_id');
    }

    public function ratesFrom(): HasMany
    {
        return $this->hasMany(Rate::class, 'from_company_id');
    }

    public function ratesTo(): HasMany
    {
        return $this->hasMany(Rate::class, 'to_company_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_MAIN => 'Main Company',
            self::TYPE_LTD => 'Limited Company',
            self::TYPE_SELF_EMPLOYED => 'Self-employed',
        ];
    }
}
