<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Campos preenchíveis via mass assignment
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_admin',
    ];

    /**
     * Campos escondidos
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts automáticos
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    /**
     * Relacionamento com Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function integrationAccounts()
    {
        return $this->tenant?->integrationAccounts();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }
}