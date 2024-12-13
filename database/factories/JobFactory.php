<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'type' => fake()->randomElement(['part_time','full_time', 'freelance']),
            'experience_level' => fake()->randomElement(['junior','mid-senior', 'senior']),
            'summary' => fake()->paragraph(),
            'salary' => fake()->numberBetween(500, 10000),
            'work_location' => fake()->randomElement(['remote', 'hybrid', 'onsite']),
            'requirements' => fake()->sentence(),
            'responsibilities' => fake()->paragraph(2),
            'company_id' => Company::factory(),
        ];
    }
}
