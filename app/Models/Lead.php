<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LeadStatus;

class Lead extends Model
{
    protected $fillable = [
        'tenant_id',
        'flux_id',
        'name',
        'phone',
        'email',
        'source',
        'status',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'status' => LeadStatus::class,
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function flux()
    {
        return $this->belongsTo(Flux::class);
    }
}