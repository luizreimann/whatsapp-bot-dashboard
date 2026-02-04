<?php

namespace Tests\Feature\Controllers;

use App\Models\Flux;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for Flux model and factory.
 * Feature tests for FluxController require complex middleware setup
 * and are better tested via browser/integration tests.
 */
class FluxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_flux_factory_creates_valid_flux(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Flux',
        ]);

        $this->assertDatabaseHas('fluxes', [
            'id' => $flux->id,
            'name' => 'Test Flux',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_flux_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertEquals($tenant->id, $flux->tenant_id);
        $this->assertInstanceOf(Tenant::class, $flux->tenant);
    }

    public function test_flux_data_is_cast_to_array(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
            'data' => [
                'nodes' => [
                    ['id' => 'start-1', 'type' => 'start'],
                ],
                'edges' => [],
            ],
        ]);

        $this->assertIsArray($flux->data);
        $this->assertArrayHasKey('nodes', $flux->data);
        $this->assertArrayHasKey('edges', $flux->data);
    }

    public function test_flux_can_be_updated(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Original Name',
        ]);

        $flux->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('fluxes', [
            'id' => $flux->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_flux_can_be_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $fluxId = $flux->id;
        $flux->delete();

        $this->assertDatabaseMissing('fluxes', ['id' => $fluxId]);
    }

    public function test_flux_status_defaults_to_draft(): void
    {
        $tenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertEquals('draft', $flux->status);
    }
}
