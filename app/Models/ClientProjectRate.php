<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProjectRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'project_id',
        'worker_id',
        'rate_type',
        'rate_amount',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'rate_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
