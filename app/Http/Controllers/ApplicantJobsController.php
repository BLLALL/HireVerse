<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\ApplicantJobResource;
use App\Models\Application;
use App\Models\Job;
use App\Pipelines\Filters\ApplicationFilters\Status;
use App\Pipelines\Filters\JobFilters\Search;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Pipeline;

class ApplicantJobsController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $applicant = Auth::user();

        $query = $applicant->jobs()
            ->with('company:id,name')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.company_id',
                'applications.created_at as applied_at',
                'applications.status',
                'applications.cv',
                'applications.cv_score',
            )->getQuery();

        $applicantJobs = Pipeline::send($query)
            ->through([Status::class, Search::class])
            ->thenReturn()->paginate(10);

        return ApplicantJobResource::collection($applicantJobs);
    }

    public function store(StoreJobApplicationRequest $request, Job $job): mixed
    {
        $attributes = $request->validated();

        $exists = Application::where([
            ["job_id", $attributes["job_id"] = $job->id],
            ["applicant_id", $attributes["applicant_id"] = Auth::id()],
        ])->exists();

        if ($exists) {
            return $this->error("You have already applied to this job before!", 422);
        }

        $attributes["cv"] = $request->file("cv")->store("applications");
        $application = Application::create($attributes);

        return $this->success("You have applied to this job successfully!", $application, 201);
    }
}
