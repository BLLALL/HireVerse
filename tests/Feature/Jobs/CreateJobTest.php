<?php

declare(strict_types=1);

use App\Models\Applicant;
use App\Models\Company;
use App\Traits\TokenHelpers;

uses(TokenHelpers::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->applicant = Applicant::factory()->create();
    $this->token = $this->company->createToken('test_token')->plainTextToken;

    $this->validJob = [
        'title' => 'LLM Machine Learning Engineer',
        'type' => 'full_time',
        'experience_level' => 'senior',
        'summary' => 'We are looking for a Machine Learning Engineer to join our team.',
        'salary' => 100000,
        'currency' => 'USD',
        'work_hours' => 'fixed_schedule',
        'work_location' => 'onsite',
        'requirements' => 'PhD in Computer Science, 5 years of experience in Machine Learning.',
        'responsibilities' => 'Develop machine learning models, collaborate with the research team.',
        'is_available' => true,
        'available_to' => now()->addDays(30)->toDateTimeString(),
        'max_applicants' => 10,
        'skills' => ['ML', 'Python', 'TensorFlow'],
    ];
});

it('prevents unauthorized users from creating a job', function () {
    $token = $this->generateToken($this->applicant);
    $response = $this->postJson('api/jobs', $this->validJob, [
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json',
    ]);
    $response->assertUnauthorized();
});
