<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Lead;
use App\Models\Flux;
use Faker\Factory as Faker;

class LeadsSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (! $tenant) {
            $this->command->warn('Nenhum tenant encontrado. Rode primeiro o InitialSetupSeeder.');
            return;
        }

        $fluxes = Flux::where('tenant_id', $tenant->id)->get();

        if ($fluxes->isEmpty()) {
            $this->command->warn('Nenhum fluxo encontrado para o tenant. Rode primeiro o InitialSetupSeeder.');
            return;
        }

        $faker = Faker::create('pt_BR');

        $statuses = ['new', 'qualified', 'in_progress', 'lost'];
        $sources  = ['whatsapp', 'landing_page', 'import'];

        $totalLeads = 60;

        for ($i = 0; $i < $totalLeads; $i++) {
            $flux = $fluxes->random();

            $createdAt = $faker->dateTimeBetween('-3 days', 'now');

            Lead::create([
                'tenant_id' => $tenant->id,
                'flux_id'   => $flux->id,
                'name'      => $faker->name(),
                'phone'     => $faker->numerify('55##########'),
                'email'     => $faker->boolean(80) ? $faker->safeEmail() : null,
                'status'    => $faker->randomElement($statuses),
                'source'    => $faker->randomElement($sources),
                'data'      => [
                    'notes'        => $faker->boolean(70) ? $faker->sentence(8) : null,
                    'utm_source'   => $faker->randomElement(['ads', 'organic', 'referral']),
                    'last_contact' => $faker->dateTimeBetween('-10 days', 'now')->format('Y-m-d H:i:s'),
                ],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info("{$totalLeads} leads criados para o tenant {$tenant->name}.");
    }
}