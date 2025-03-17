<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Models\Job;
use App\Models\Skill;
use Database\Seeders\JobSeeder;

use function Pest\Laravel\getJson;

it('filters jobs by work type', function () {

    Job::factory()->create(['type' => 'full_time']);
    Job::factory()->create(['type' => 'part_time']);
    Job::factory()->create(['type' => 'part_time']);

    $response = getJson('/api/jobs?type=full_time');

    $response->assertJsonCount(1, 'data')->assertJsonFragment([
        'type' => 'full_time',
    ]);
    $response = getJson('/api/jobs?type=part_time');
    $response->assertJsonCount(2, 'data')->assertJsonFragment([
        'type' => 'part_time',
    ]);
});

it('filters jobs by experience level', function () {

    Job::factory()->create(['experience_level' => 'junior']);
    Job::factory()->create(['experience_level' => 'senior']);
    Job::factory()->create(['experience_level' => 'senior']);

    $response = getJson('/api/jobs?experience_level=junior');

    $response->assertJsonCount(1, 'data')->assertJsonFragment([
        'experienceLevel' => 'junior',
    ]);

    $response = getJson('/api/jobs?experience_level=senior');

    $response->assertJsonCount(2, 'data')->assertJsonFragment([
        'experienceLevel' => 'senior',
    ]);
});

it('filters jobs by work location', function () {
    request()->merge(['location' => 'remote']);

    Job::factory()->create(['work_location' => 'remote']);
    Job::factory()->create(['work_location' => 'onsite']);

    $filteredJobs = getJson('/api/jobs?location=remote');

    $filteredJobs->assertJsonCount(1, 'data')->assertJsonFragment([
        'workLocation' => 'remote',
    ]);
});

it('it applies multiple filterable filters', function () {
    $this->seed(JobSeeder::class);

    $response = getJson('/api/jobs?'.http_build_query([
        'type' => 'full_time,part_time',
        'experience_level' => 'senior,mid-level',
        'location' => 'remote,hybrid',
    ]));

    $response->assertJsonMissing([
        'type' => 'freelance',
        'experienceLevel' => 'junior',
        'workLocation' => 'onsite',
    ]);

});

it('filters jobs by work schedule type', function () {

    $flexibleJob = Job::factory()->create(['work_hours' => 'flexible_schedule']);
    Job::factory()->create(['work_hours' => 'fixed_schedule']);

    $response = getJson('/api/jobs?work_hours=flexible_schedule');

    $response->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $flexibleJob->id,
        ]);
});

it('filters by salary range', function () {
    $lowSalaryJob = Job::factory()->create(['salary' => 1000]);
    $midSalaryJob = Job::factory()->create(['salary' => 5000]);
    $highSalaryJob = Job::factory()->create(['salary' => 10000]);

    // specifying minimum salary only
    $response = getJson('/api/jobs?min_salary=5000');

    $response->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'id' => $midSalaryJob->id,
        ])
        ->assertJsonFragment([
            'id' => $highSalaryJob->id,
        ]);

    // specifying maximum salary only
    $response = getJson('/api/jobs?max_salary=5000');

    $response->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'id' => $lowSalaryJob->id,
        ])
        ->assertJsonFragment([
            'id' => $midSalaryJob->id,
        ]);

    // specifying both minimum and maximum salary
    $response = getJson('/api/jobs?min_salary=1000&max_salary=10000');

    $response->assertJsonCount(3, 'data')
        ->assertJsonFragment([
            'id' => $lowSalaryJob->id,
        ])
        ->assertJsonFragment([
            'id' => $midSalaryJob->id,
        ])
        ->assertJsonFragment([
            'id' => $highSalaryJob->id,
        ]);

});

it('filters by search term', function () {
    $phpJob = Job::factory()->create(['title' => 'PHP Developer']);
    $javaJob = Job::factory()->create(['title' => 'Java Developer']);

    $skill = Skill::create([
        'title' => 'OOP',
        'skillable_id' => $javaJob->id,
        'skillable_type' => Job::class,
    ]);

    $response = getJson('/api/jobs?search=php');

    $response->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $phpJob->id,
        ]);

    $response = getJson('/api/jobs?search=oop');
    $response->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $javaJob->id,
        ]);

});

it('correctly applies multiple filters', function () {

    $matchingJob = Job::factory()->create([
        'title' => 'PHP Developer',
        'experience_level' => 'senior',
        'work_location' => 'hybrid',
        'type' => 'full_time',
    ]);

    Job::factory()->create([
        'title' => 'Java Developer',
        'experience_level' => 'junior',
        'work_location' => 'onsite',
        'type' => 'part_time',
    ]);

    $response = getJson('/api/jobs?search=php&experience_level=senior&location=hybrid&type=full_time');
    $response->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $matchingJob->id,
        ]);
});
