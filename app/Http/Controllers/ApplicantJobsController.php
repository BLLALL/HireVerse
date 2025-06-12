<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Events\ApplicantApplied;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\ApplicantJobResource;
use App\Models\Application;
use App\Models\Job;
use App\Pipelines\Filters\ApplicationFilters\Status;
use App\Pipelines\Filters\JobFilters\Search;
use App\Traits\ApiResponses;
use App\Traits\FileHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

class ApplicantJobsController extends Controller
{
    use ApiResponses, FileHelpers;

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

        if (! $job->is_available) {
            return $this->error('This job is no longer accepting applications!', 410);
        }

        $exists = Application::where([
            ['job_id', $attributes['job_id'] = $job->id],
            ['applicant_id', $attributes['applicant_id'] = Auth::id()],
        ])->exists();

        if ($exists) {
            return $this->error('You have already applied to this job before!', 422);
        }

        $cvFile = $request->file('cv');
        $attributes['cv'] = $cvFile->storeAs('applications', $this->generateUniqueName($cvFile));

        $application = Application::create($attributes);

        ApplicantApplied::dispatch($job);

        return $this->success('You have applied to this job successfully!', $application, 201);
    }

    public function getUpcomingInterviews(Request $request)
    {
        $applicant = $request->user();

        $upcomingInterviews = DB::table('interviews')
            ->select(
                'interviews.id as interview_id',
                'interviews.deadline',
                'jobs.title as job_title',
                'companies.name as company_name',

            )
            ->join('applications', 'interviews.application_id', '=', 'applications.id')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->where('applications.applicant_id', $applicant->id)
            ->where('applications.status', ApplicationStatus::InterviewScheduled)
            ->where('interviews.deadline', '>', now())
            ->orderBy('interviews.deadline', 'asc')
            ->get();

        return response()->json([
            'upcoming_interviews' => $upcomingInterviews,
        ]);
    }
}
