<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'engineer_id',
        'certification_type_id',
        'issue_date',
        'expiry_date',
        'file_path',
        'mime_type',
        'verified',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified' => 'boolean',
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CertificationType::class, 'certification_type_id');
    }
}
