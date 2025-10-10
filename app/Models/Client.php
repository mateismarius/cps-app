<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
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
        'payment_terms_days',
        'contacts',
        'status',
    ];

    protected $casts = [
        'contacts' => 'array',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function projectRates()
    {
        return $this->hasMany(ClientProjectRate::class);
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }
}
