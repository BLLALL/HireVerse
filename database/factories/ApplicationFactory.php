<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cv' => fake()->filePath(),
            'status' => $status = fake()->randomElement(ApplicationStatus::values()),
            'cv_score' => $status != ApplicationStatus::Pending ? fake()->numberBetween(10, 100): null,
            'inteview_date' => $status == ApplicationStatus::Eligible ? fake()->date('+6 months'): null,
        ];
    }
}
