<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'document', 'document_type',
        'phone', 'email', 'segment', 'address',
    ];

    protected function casts(): array
    {
        return ['address' => 'array'];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
