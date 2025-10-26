<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'project_id',
        'issuer_company_id',
        'receiver_company_id',
        'period_start',
        'period_end',
        'total_amount',
        'status',
        'issued_at',
        'due_at',
        'file_path',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'issuer_company_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'receiver_company_id');
    }
}
