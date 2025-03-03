<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobResource;
use App\Models\{Application, Job};
use App\Traits\ApiResponses;

class JobController extends Controller
{
    use ApiResponses;

    public function index(): mixed
    {
        return JobResource::collection(Job::all());
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
            ["job_id", $attributes["job_id"]],
            ["applicant_id", $attributes["applicant_id"]],
        ])->exists();

        if ($exists) {
            return $this->error("You have already applied to this job before!", 422);
        }

        $cv = $request->file("cv")->store("applications");
        $attributes["cv"] = $cv;

        $application = Application::create($attributes);

        return $this->success("You have applied to this job successfully!", $application, 201);
    }
}
