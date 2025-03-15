<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobResource;
use App\Models\Application;
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

    public function apply(StoreJobApplicationRequest $request): mixed
    {
        $attributes = $request->validated();

        $attributes['applicant_id'] = auth()->id();

        $exists = Application::where([
            ['job_id', $attributes['job_id']],
            ['applicant_id', $attributes['applicant_id']],
        ])->exists();

        if ($exists) {
            return $this->error('You have already applied to this job before!', 422);
        }

        $cv = $request->file('cv')->store('applications');
        $attributes['cv'] = $cv;

        $application = Application::create($attributes);

        return $this->success('You have applied to this job successfully!', $application, 201);
    }
}
