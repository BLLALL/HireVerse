<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Enums\JobPhase;
use App\Models\Application;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\DB;
use App\Events\InterviewPhaseStarted;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Pipeline;
use App\Http\Resources\CompanyJobsResource;
use App\Http\Resources\CompanyStatsResource;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Resources\CompanyJobApplicationResource;
use App\Pipelines\Filters\JobFilters\SearchApplicants;

class CompanyJobsController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $company = auth()->user();

        return [
            'stats' => new CompanyStatsResource($company),
            'jobs' => CompanyJobsResource::collection(
                $company->jobs()
                    ->with('company')
                    ->latest()
                    ->get()
            ),
        ];
    }

    public function applicants(Job $job)
    {
        $this->authorize($job);

        $query = $job->applicants()->select('first_name', 'last_name', 'email', 'applications.*')->getQuery();
        $applicants = Pipeline::send($query)->through(SearchApplicants::class)->thenReturn()->paginate(10);

        return CompanyJobApplicationResource::collection($applicants)->additional([
            'jobPhase' => $job->phase,
        ]);
    }

    public function setMinScore(Request $request, Job $job)
    {
        $this->authorize($job);

        if ($job->phase == JobPhase::CVFiltration) {
            return $this->error("Some CVs haven't been filtered yet!", 409);
        }
        
        if ($job->phase == JobPhase::Interview) {
            return $this->error("Minimum CV score has already been set!", 409);
        }

        $minCVScore = $request->validate(['min_score' => 'required|decimal:0,2|min:1|max:100'])['min_score'];

        Application::whereJobId($job->id)->where('cv_score', '>=', $minCVScore)->update(['status' => ApplicationStatus::CVEligible]);
        Application::whereJobId($job->id)->where('cv_score', '<', $minCVScore)->update(['status' => ApplicationStatus::CVRejected]);

        $job->update(['phase' => JobPhase::Interview]);

        InterviewPhaseStarted::dispatch($job);

        return $this->ok('Interview phase started.');
    }

      public function getCompletedInterviews(Request $request, Job $job)
    {
        $company = $request->user();

        $interviewedApplicants = DB::table('applicants')
            ->select(
                'applicants.id as applicant_id',
                'applicants.first_name',
                'applicants.last_name',
                'interviews.id as interview_id',
                'interviews.technical_skills_score as technical_score',
                'interviews.soft_skills_score as soft_score',
            )
            ->join('applications', 'applicants.id', '=', 'applications.applicant_id')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->join('interviews', 'applications.id', '=', 'interviews.application_id')
            ->where('jobs.company_id', $company->id)
            ->where('jobs.id', $job->id)
            ->whereNotNull('interviews.technical_skills_score')
            ->get();

        $interviewedApplicants = $interviewedApplicants->map(function ($interview) {
           $answersDir = "interviews/{$interview->interview_id}/answers";

           $files = Storage::files($answersDir);
            $interview->video_urls = array_map(function ($file) use ($answersDir) {
                return Storage::url($file);
            }, $files);

            return $interview;
        });
        return response()->json($interviewedApplicants);
    }

    public function authorize(Job $job)
    {
        if ($job->company_id != auth()->id()) {
            throw new AuthorizationException;
        }
    }
}
