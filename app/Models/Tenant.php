<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

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

    public function integrationAccounts()
    {
        return $this->hasMany(IntegrationAccount::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
