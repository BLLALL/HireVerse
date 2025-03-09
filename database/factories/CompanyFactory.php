<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'password' => (static::$password ??= Hash::make('password')),
            'location' => fake()->randomElement([
                'Toukh',
                'Manshia El3mar',
                'Benha',
                'Elqanater Elkherya',
            ]),
            'website_url' => fake()->url(),
            'ceo' => fake()->name(),
            'description' => fake()->paragraph(),
            'insights' => fake()->paragraph(),
            'industry' => fake()->randomElement([
                'Technology,Software',
                'Healthcare,Medical Devices',
                'Retail,Online Shopping',
                'Education,Elearning',
                'Finance,FinTech',
                'Real Estate,Property Management',
                'Manufacturing,Industrial Equipment',
                'Transportation,Logistics',
                'Entertainment,Media Production',
                'Tourism',
                'Food & Beverage,Restaurants',
                'Energy,Renewable Energy',
                'Construction,Building Materials',
                'Agriculture,Farming Technology',
                'Telecommunications,Mobile Networks',
            ]),
            'employee_no' => fake()->numberBetween(3, 100000),
            'logo' => 'https://loremflickr.com/50/50/logo',
        ];
    }
}
