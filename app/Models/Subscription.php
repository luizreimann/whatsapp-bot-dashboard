<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'status',
        'payment_method',
        'external_subscription_id',
        'external_customer_id',
        'billing_cycle',
        'current_period_start',
        'current_period_end',
        'amount',
        'currency',
        'canceled_at',
        'suspended_at',
    ];

    protected function casts(): array
    {
        return [
            'current_period_start' => 'date',
            'current_period_end' => 'date',
            'amount' => 'decimal:2',
            'canceled_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('current_period_end', '<', now()->subDays(7));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isExpired(): bool
    {
        return $this->status === 'active' 
            && $this->current_period_end 
            && $this->current_period_end->lt(now()->subDays(7));
    }
}
