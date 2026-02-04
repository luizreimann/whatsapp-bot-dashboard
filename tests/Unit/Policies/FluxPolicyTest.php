<?php

namespace Tests\Unit\Policies;

use App\Models\Flux;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\FluxPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FluxPolicyTest extends TestCase
{
    use RefreshDatabase;

    private FluxPolicy $policy;
    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new FluxPolicy();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_user_can_view_own_tenant_flux(): void
    {
        $flux = Flux::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($this->policy->view($this->user, $flux));
    }

    public function test_user_cannot_view_other_tenant_flux(): void
    {
        $otherTenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $this->assertFalse($this->policy->view($this->user, $flux));
    }

    public function test_user_can_update_own_tenant_flux(): void
    {
        $flux = Flux::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($this->policy->update($this->user, $flux));
    }

    public function test_user_cannot_update_other_tenant_flux(): void
    {
        $otherTenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $this->assertFalse($this->policy->update($this->user, $flux));
    }

    public function test_user_can_delete_own_tenant_flux(): void
    {
        $flux = Flux::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($this->policy->delete($this->user, $flux));
    }

    public function test_user_cannot_delete_other_tenant_flux(): void
    {
        $otherTenant = Tenant::factory()->create();
        $flux = Flux::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $this->assertFalse($this->policy->delete($this->user, $flux));
    }
}
