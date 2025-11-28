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
        ];
    }

    /**
     * Relacionamento com Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}