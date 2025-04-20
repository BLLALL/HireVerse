<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use Carbon\Carbon;
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
            'cv_score' => ! in_array($status, [
                'Pending',
                'CV processing'
            ]) ? fake()->numberBetween(10, 100) : null,

            'interview_date' => in_array($status, [
                'Interview scheduled',
                'Interviewed',
                'Accepted',
                'Rejected'
            ]) ? Carbon::make(now()->addDays(random_int(5, 50)))->toDateString() : null,
        ];
    }
}
