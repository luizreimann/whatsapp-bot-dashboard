<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function whatsappInstance()
    {
        return $this->hasOne(WhatsappInstance::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function fluxes()
    {
        return $this->hasMany(Flux::class);
    }
}
