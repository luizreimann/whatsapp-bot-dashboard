<?php

namespace App\Models;

use App\Enums\IntegrationCategory;
use App\Enums\IntegrationProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class IntegrationAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'category',
        'provider',
        'name',
        'config',
        'metadata',
        'status',
        'connected_at',
        'last_synced_at',
    ];

    protected $casts = [
        'config'         => 'array',
        'metadata'       => 'array',
        'connected_at'   => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    /*
     * Relacionamento com Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /*
     * Accessor/Mutator para provider como Enum
     */
    protected function providerEnum(): Attribute
    {
        return Attribute::make(
            get: fn () => IntegrationProvider::from($this->provider),
            set: fn (IntegrationProvider $provider) => $provider->value,
        );
    }

    /*
     * Accessor/Mutator para category como Enum
     */
    protected function categoryEnum(): Attribute
    {
        return Attribute::make(
            get: fn () => IntegrationCategory::from($this->category),
            set: fn (IntegrationCategory $category) => $category->value,
        );
    }

    /*
     * Helpers
     */
    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function markConnected(): void
    {
        $this->status = 'connected';
        $this->connected_at = now();
        $this->save();
    }

    public function markDisconnected(): void
    {
        $this->status = 'disconnected';
        $this->save();
    }

    public function markError(string $message = null): void
    {
        $this->status = 'error';

        $meta = $this->metadata ?? [];
        if ($message) {
            $meta['last_error'] = $message;
            $meta['last_error_at'] = now()->toDateTimeString();
        }

        $this->metadata = $meta;
        $this->save();
    }
}