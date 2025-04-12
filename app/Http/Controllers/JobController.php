<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Pipelines\Filters\JobFilters\ExperienceLevel;
use App\Pipelines\Filters\JobFilters\JobType;
use App\Pipelines\Filters\JobFilters\Location;
use App\Pipelines\Filters\JobFilters\RangeSalary;
use App\Pipelines\Filters\JobFilters\Search;
use App\Pipelines\Filters\JobFilters\WorkingHours;
use App\Traits\ApiResponses;

class JobController extends Controller
{
    use ApiResponses;

    public function index(): mixed
    {
        $jobs = Job::query()->filter([
            Location::class,
            Search::class,
            JobType::class,
            ExperienceLevel::class,
            RangeSalary::class,
            WorkingHours::class,
        ]);

        return JobResource::collection($jobs->latest()->get());
    }

    public function show(Job $job): mixed
    {
        return JobResource::make($job);
    }

    public function store(StoreJobRequest $request): mixed
    {
        $company = $this->getAuthenticatedCompany($request);
        if (! $company) {
            return $this->error('You are not authorized to create a job', 403);
        }

        $job = $this->createJob($company, $request->validated());
        $this->attachSkills($job, $request->validated()['skills'] ?? []);

        return JobResource::make($job)->additional([
            'message' => 'Job created successfully',
        ]);
    }


    private function getAuthenticatedCompany($request): ?\App\Models\Company
    {
        $tokenable = $request->user()?->currentAccessToken()->tokenable;

        return $tokenable instanceof \App\Models\Company ? $tokenable : null;
    }

    private function createJob($company, array $attributes): Job
    {
        return $company->jobs()->create($attributes);
    }

    private function attachSkills(Job $job, array $skills): void
    {
        collect($skills)->each(
            fn ($skillTitle) => $job->skills()->create(['title' => $skillTitle])
        );
    }
}
