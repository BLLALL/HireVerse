<?php

declare(strict_types=1);

use App\Models\Job;
use App\Models\Company;
use App\Models\Applicant;
use App\Traits\TokenHelpers;
use Laravel\Sanctum\Sanctum;

uses(TokenHelpers::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->applicant = Applicant::factory()->create();
    $this->token = Sanctum::actingAs($this->company);


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
        'available_to' => now()->addDays(30)->toDateString(),
        'max_applicants' => 10,
        'skills' => ['ML', 'Python', 'TensorFlow'],
    ];
});

it('prevents unauthorized users from creating a job', function () {
    // \DB::enableQueryLog();
    $token = $this->generateToken($this->applicant);
    $response = $this->postJson('api/jobs', $this->validJob, [
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('jobs', [
        'title' => $this->validJob['title'],
    ]);
});

it('validates field types and constraints', function () {
    $invalidJobData = array_merge($this->validJob, [
        'type' => 'invalid',
        'experience_level' => 'invalid',
        'salary' => 'NaN',
        'max_applicants' => -1,
        'skills' => 'not_an_array',
    ]);

    $response = $this->postJson('api/jobs', $invalidJobData, [
        'Authorization' => 'Bearer '.$this->token,
        'Accept' => 'application/json',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'type',
            'experience_level',
            'salary',
            'max_applicants',
            'skills',
        ]);
});

it('creates a job successfully', function () {
    
    $response = $this->postJson('api/jobs', $this->validJob, [
        'Authorization' => 'Bearer '.$this->token,
        'Accept' => 'application/json',
    ]);
    $response
        ->assertCreated()
        ->assertJsonFragment(collect($this->validJob)
            ->except('available_to', 'experience_level', 'work_location',
                'work_hours', 'max_applicants', 'is_available')->toArray())
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'title',
                    'type',
                    'experienceLevel',
                    'summary',
                    'salary',
                    'currency',
                    'workHours',
                    'workLocation',
                    'requirements',
                    'responsibilities',
                    'isAvailable',
                    'availableTo',
                    'maxApplicants',
                    'skills',
                    'created',
                    'updated',
                ],
                'relationships' => [
                    'company' => [
                        'data' => ['type', 'id'],
                    ],
                ],
            ],
        ]);

    $this->assertDatabaseHas('jobs', [
        'title' => $this->validJob['title'],
        'company_id' => $this->company->id,
    ]);

    $job = Job::where('title', $this->validJob['title'])->first();

    expect($job->skills->toArray())->toEqual(
        $this->validJob['skills']
    );
});
