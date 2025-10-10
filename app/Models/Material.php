<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'unit',
        'quantity',
        'minimum_quantity',
        'unit_cost',
        'supplier',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($material) {
            if ($material->quantity <= 0) {
                $material->status = 'out_of_stock';
            } elseif ($material->quantity <= $material->minimum_quantity) {
                $material->status = 'low_stock';
            } else {
                $material->status = 'in_stock';
            }
        });
    }
}
