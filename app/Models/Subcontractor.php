<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Subcontractor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'relationship_type',
        'parent_subcontractor_id',
        'business_type',
        'registration_number',
        'vat_number',
        'email',
        'phone',
        'address',
        'city',
        'postcode',
        'bank_details',
        'status',
        'company_id',
    ];

    protected $casts = [
        'bank_details' => 'array',
    ];

    public function parentSubcontractor()
    {
        return $this->belongsTo(Subcontractor::class, 'parent_subcontractor_id');
    }

    public function childSubcontractors()
    {
        return $this->hasMany(Subcontractor::class, 'parent_subcontractor_id');
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function certifications()
    {
        return $this->morphMany(Certification::class, 'certifiable');
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Users linked to this subcontractor
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subcontractor) {
            // Delete associated workers and their rates
            foreach ($subcontractor->workers as $worker) {
                $worker->rates()->delete();
                $worker->delete();
            }
        });
    }
}
