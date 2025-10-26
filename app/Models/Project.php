<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use function Laravel\Prompts\select;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_company_id',
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'billing_type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'main_company_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ON_HOLD = 'on_hold';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Billing type constants
    public const BILLING_SHIFTS = 'shifts';
    public const BILLING_FIXED = 'fixed';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getBillingTypeOptions(): array
    {
        return [
            self::BILLING_SHIFTS => 'Shifts',
            self::BILLING_FIXED => 'Fixed Price',
        ];
    }
}
