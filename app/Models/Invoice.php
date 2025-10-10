<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoiceable_type',
        'invoiceable_id',
        'invoice_type',
        'invoice_date',
        'due_date',
        'subtotal',
        'vat_amount',
        'vat_rate',
        'total_amount',
        'status',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('amount');
        $this->vat_amount = $this->subtotal * ($this->vat_rate / 100);
        $this->total_amount = $this->subtotal + $this->vat_amount;
        $this->save();
    }
}
