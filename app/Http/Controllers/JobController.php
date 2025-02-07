<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobResource;
use App\Models\Application;
use App\Models\Job;

class JobController extends Controller
{
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

        // $attributes['applicant_id'] = auth()->id();
        $attributes['applicant_id'] = 1;

        $exists = Application::where([
            ['job_id', $attributes['job_id']],
            ['applicant_id', $attributes['applicant_id']]
        ])->exists();

        if ($exists) {
            return response()->json([
                'status' => 422,
                'message' => 'You have already applied to this job before 🤗!'
            ], 422);
        }

        $cv = $request->file('cv')->store('applications');
        $attributes['cv'] = $cv;

        Application::create($attributes);

        return response()->json([
            'status' => 201,
            'message' => 'You have applied to this job successfully 🤗!'
        ], 201);
    }
}
