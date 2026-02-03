<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flux extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'data',
        'conversion_goal',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}