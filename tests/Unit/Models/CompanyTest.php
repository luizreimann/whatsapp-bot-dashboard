<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_be_created(): void
    {
        $tenant = Tenant::factory()->create();

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Teste',
            'document'  => '11222333000181',
            'phone'     => '11999999999',
            'email'     => 'contato@empresa.com',
            'segment'   => 'tecnologia',
        ]);

        $this->assertDatabaseHas('companies', [
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Teste',
        ]);
    }

    public function test_company_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Teste',
        ]);

        $this->assertInstanceOf(Tenant::class, $company->tenant);
        $this->assertEquals($tenant->id, $company->tenant->id);
    }

    public function test_tenant_has_one_company(): void
    {
        $tenant = Tenant::factory()->create();

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Teste',
        ]);

        $this->assertInstanceOf(Company::class, $tenant->company);
        $this->assertEquals($company->id, $tenant->company->id);
    }

    public function test_address_is_cast_to_array(): void
    {
        $tenant = Tenant::factory()->create();

        $address = [
            'zip'          => '01001000',
            'street'       => 'Rua das Flores',
            'number'       => '123',
            'complement'   => 'Sala 4',
            'neighborhood' => 'Centro',
            'city'         => 'São Paulo',
            'state'        => 'SP',
        ];

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Teste',
            'address'   => $address,
        ]);

        $company->refresh();

        $this->assertIsArray($company->address);
        $this->assertEquals('01001000', $company->address['zip']);
        $this->assertEquals('São Paulo', $company->address['city']);
    }

    public function test_company_can_be_created_without_optional_fields(): void
    {
        $tenant = Tenant::factory()->create();

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Mínima',
        ]);

        $this->assertDatabaseHas('companies', [
            'tenant_id' => $tenant->id,
            'name'      => 'Empresa Mínima',
            'document'  => null,
            'phone'     => null,
            'email'     => null,
            'segment'   => null,
            'address'   => null,
        ]);
    }
}
