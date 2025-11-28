<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    protected $fillable = [
        'tenant_id',
        'status',
        'bot_token',
        'number',
        'fly_app_name',
        'public_url',
        'last_status_payload',
        'last_connected_at',
    ];

    protected $casts = [
        'last_status_payload' => 'array',
        'last_connected_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}