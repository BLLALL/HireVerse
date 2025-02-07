<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ApplicantFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'birthdate' => fake()->date(max: 'now'),
            'college' => fake()->randomElement(['Faculty of Science', 'Faculty of Computers and Information', 'Faculty of Engineering']),
            'department' => fake()->randomElement(['Electricity', 'Computer Science', 'Artificial Intelligence']),
            'cv' => fake()->filePath(),
            'job_title' => fake()->randomElement([
                'Backend developer',
                'Frontend Developer',
                'UI/UX Designer',
                'ML Developer',
                'Data Scientist',
                'Data Analyst',
                'Data Engineer',
                'Application Developer',
                'Embedded-Systems Engineer',
                'Cyber security Engineer',
                'Game Developer',
            ]),
            'github_url' => fake()->randomElement(['github.com/BLLALL', 'github.com/smoawad66']),
            'linkedin_url' => fake()->url(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
