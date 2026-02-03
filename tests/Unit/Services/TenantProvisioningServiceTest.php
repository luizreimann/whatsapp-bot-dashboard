<?php

namespace Tests\Unit\Services;

use App\Models\Tenant;
use App\Models\WhatsappInstance;
use App\Models\Flux;
use App\Services\Payment\TenantProvisioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantProvisioningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TenantProvisioningService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TenantProvisioningService();
    }

    public function test_provision_creates_whatsapp_instance(): void
    {
        $tenant = Tenant::factory()->create();

        $this->service->provision($tenant);

        $this->assertDatabaseHas('whatsapp_instances', [
            'tenant_id' => $tenant->id,
            'status' => 'inactive',
        ]);
    }

    public function test_provision_creates_welcome_flux(): void
    {
        $tenant = Tenant::factory()->create();

        $this->service->provision($tenant);

        $this->assertDatabaseHas('fluxes', [
            'tenant_id' => $tenant->id,
            'name' => 'Fluxo de Boas-vindas',
            'status' => 'draft',
        ]);
    }

    public function test_provision_does_not_duplicate_whatsapp_instance(): void
    {
        $tenant = Tenant::factory()->create();
        WhatsappInstance::factory()->create(['tenant_id' => $tenant->id]);

        $this->service->provision($tenant);

        $this->assertEquals(1, WhatsappInstance::where('tenant_id', $tenant->id)->count());
    }

    public function test_provision_does_not_duplicate_welcome_flux(): void
    {
        $tenant = Tenant::factory()->create();
        Flux::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Fluxo de Boas-vindas',
        ]);

        $this->service->provision($tenant);

        $this->assertEquals(1, Flux::where('tenant_id', $tenant->id)
            ->where('name', 'Fluxo de Boas-vindas')
            ->count());
    }

    public function test_suspend_updates_tenant_status(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);

        $this->service->suspend($tenant);

        $this->assertEquals('suspended', $tenant->fresh()->status);
    }

    public function test_reactivate_updates_tenant_status(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'suspended']);

        $this->service->reactivate($tenant);

        $this->assertEquals('active', $tenant->fresh()->status);
    }
}
