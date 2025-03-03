<?php

namespace Database\Factories;

use App\Enums\ExperienceLevel;
use App\Enums\WorkLocation;
use App\Enums\JobType;
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
            "title" => fake()->jobTitle(),
            "type" => fake()->randomElement(JobType::values()),
            "experience_level" => fake()->randomElement(ExperienceLevel::values()),
            "summary" => fake()->sentence(),
            "salary" => fake()->numberBetween(500, 10000),
            "work_location" => fake()->randomElement(WorkLocation::values()),
            "requirements" => fake()->sentence(),
            "responsibilities" => fake()->paragraph(2),
            "available_to" => fake()->dateTimeBetween("now", "+2 month"),
            "max_applicants" => fake()->numberBetween(50, 500),
            "company_id" => Company::factory(),
        ];
    }
}
