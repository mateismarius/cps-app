<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'trading_name',
        'registration_number',
        'vat_number',
        'email',
        'phone',
        'address',
        'city',
        'postcode',
        'country',
        'bank_details',
        'status',
    ];

    protected $casts = [
        'bank_details' => 'array',
    ];

//    public function certifications()
//    {
//        return $this->morphMany(Certification::class, 'certifiable');
//    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
