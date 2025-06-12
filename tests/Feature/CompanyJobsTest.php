<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Applicant;
use App\Models\Company;
use App\Models\Job;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->company = Company::factory()->create();
    Sanctum::actingAs($this->company, ['*']);
});

it('returns company jobs with statistics', function () {
    // Create jobs from last month
    $lastMonthJobs = Job::factory(3)->create([
        'company_id' => $this->company->id,
        'created_at' => now()->subMonth(),
    ]);

    // Create jobs for this month
    $currentJobs = Job::factory(5)->create([
        'company_id' => $this->company->id,
    ]);

    // Create some applications
    $applicants = Applicant::factory(4)->create();

    // Add applications with specific dates for testing monthly changes
    $currentJobs->each(function ($job) use ($applicants) {
        $applicants->random(2)->each(function ($applicant) use ($job) {
            $job->applicants()->attach($applicant->id, [
                'status' => ApplicationStatus::Accepted,
                'cv' => 'test.pdf',
                'created_at' => now(),
            ]);
        });
    });

    // Add some applications for last month
    $lastMonthJobs->each(function ($job) use ($applicants) {
        $applicants->random(1)->each(function ($applicant) use ($job) {
            $job->applicants()->attach($applicant->id, [
                'status' => ApplicationStatus::Accepted,
                'cv' => 'test.pdf',
                'created_at' => now()->subMonth(),
            ]);
        });
    });

    $response = $this->getJson('/api/company/jobs')
        ->assertStatus(200)
        ->assertJsonStructure([
            'stats' => [
                'publishedJobs' => ['total', 'change'],
                'acceptedCandidates' => ['total', 'change'],
                'totalApplications' => ['total', 'change'],
            ],
            'jobs' => [
                '*' => [
                    'type',
                    'jobId',
                    'attributes' => [
                        'jobTitle',
                        'companyName',
                        'createdAt',
                        'availableTo',
                        'applicantsCount',
                        'worLdLocation',
                        'jobLocation',
                        'jobType',
                        'duration',
                    ],
                ],
            ],
        ]);

    $stats = $response->json('stats');

    // We created:
    // - 8 total jobs (5 current month + 3 last month)
    // - 10 accepted applications (2 applicants * 5 current jobs)
    // - 3 accepted applications from last month (1 applicant * 3 last month jobs)
    expect($stats['publishedJobs']['total'])->toBe(8)
        ->and($stats['publishedJobs']['change'])->toBe('+2') // 5 this month - 3 last month
        ->and($stats['acceptedCandidates']['total'])->toBe(13) // 10 current month + 3 last month
        ->and($stats['acceptedCandidates']['change'])->toBe('+7') // 10 this month - 3 last month
        ->and($stats['totalApplications']['total'])->toBe(13) // same as accepted since all are accepted
        ->and($stats['totalApplications']['change'])->toBe('+7')
        ->and($response->json('jobs'))->toHaveCount(8);
});

it('returns unauthorized for non-authenticated companies', function () {
    $this->getJson('/api/company/jobs')
        ->assertStatus(403);
});

it('only returns jobs belonging to authenticated company', function () {
    // Create jobs for authenticated company
    Job::factory(3)->create([
        'company_id' => $this->company->id,
    ]);

    // Create jobs for another company
    $otherCompany = Company::factory()->create();
    Job::factory(2)->create([
        'company_id' => $otherCompany->id,
    ]);

    $response = $this->getJson('/api/company/jobs')
        ->assertStatus(200);

    expect($response->json('jobs'))->toHaveCount(3);
});
