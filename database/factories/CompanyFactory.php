<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'tenant_id'     => Tenant::factory(),
            'name'          => fake()->company(),
            'document'      => fake()->numerify('##############'),
            'document_type' => 'cnpj',
            'phone'         => fake()->numerify('###########'),
            'email'         => fake()->companyEmail(),
            'segment'       => fake()->randomElement([
                'ecommerce', 'saude', 'educacao', 'consultoria',
                'marketing_digital', 'tecnologia', 'servicos_financeiros',
                'alimentacao', 'imobiliario', 'varejo', 'outro',
            ]),
            'address' => [
                'zip'          => fake()->numerify('########'),
                'street'       => fake()->streetName(),
                'number'       => fake()->buildingNumber(),
                'complement'   => fake()->optional()->secondaryAddress(),
                'neighborhood' => fake()->citySuffix(),
                'city'         => fake()->city(),
                'state'        => fake()->stateAbbr(),
            ],
        ];
    }
}
