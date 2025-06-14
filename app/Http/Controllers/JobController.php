<?php

namespace App\Http\Controllers;

use App\AIServices\RecommendationService;
use App\Http\Requests\StoreJobRequest;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    use ApiResponses;

    public function index(RecommendationService $recommendation): mixed
    {   
        $recommendedJobsIds = $recommendation->handle();

        $recommendedJobs = Job::with('company')->whereIn('id', $recommendedJobsIds)->get();

        $jobs = Job::available()->with('company')->whereNotIn('id', $recommendedJobsIds)->filter([
            Location::class,
            Search::class,
            JobType::class,
            ExperienceLevel::class,
            RangeSalary::class,
            WorkingHours::class,
        ]);

        return [
            'recommendedJobs' => JobResource::collection($recommendedJobs),
            'jobs' => JobResource::collection($jobs->latest()->paginate(10)),
        ];
    }

    public function show(Job $job): mixed
    {
        $applicantApplied = Application::whereApplicantId(auth()->id())->whereJobId($job->id)->exists();

        return JobResource::make($job)->additional([
            'applicantApplied' => $applicantApplied,
        ]);
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

    public function destroy(Job $job): mixed
    {
        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
