<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappInstance;
use App\Models\Flux;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Tenant
        $tenant = Tenant::create([
            'name'   => 'Empresa Teste',
            'slug'   => 'empresa-teste',
            'status' => 'active',
        ]);

        // 2. Criar Usuário vinculado ao Tenant
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Ademiro da Silva',
            'email'     => 'admin@example.com',
            'password'  => Hash::make('password'),
        ]);

        // 3. Criar WhatsappInstance inicial
        $instance = WhatsappInstance::create([
            'tenant_id'           => $tenant->id,
            'status'              => 'inactive',
            'bot_token'           => Str::random(40),
            'number'              => null,
            'fly_app_name'        => null,
            'public_url'          => null,
            'last_status_payload' => null,
        ]);

        // 4. Criar dois fluxos
        $fluxA = Flux::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Fluxo de Qualificação A',
            'status'    => 'active',
            'data'      => [
                'nodes'      => [],
                'edges'      => [],
                'version'    => 1,
                'description'=> 'Fluxo inicial de qualificação A.',
            ],
            'conversion_goal' => 0,
        ]);

        $fluxB = Flux::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Fluxo de Qualificação B',
            'status'    => 'active',
            'data'      => [
                'nodes'      => [],
                'edges'      => [],
                'version'    => 1,
                'description'=> 'Fluxo inicial de qualificação B.',
            ],
            'conversion_goal' => 0,
        ]);

        echo "\n=== SEED BASE GERADO COM SUCESSO ===\n";
        echo "Tenant ID: {$tenant->id}\n";
        echo "User login: admin@example.com / password\n";
        echo "WhatsApp Bot Token: {$instance->bot_token}\n";
        echo "Fluxos criados: {$fluxA->name} e {$fluxB->name}\n";
        echo "====================================\n";
    }
}