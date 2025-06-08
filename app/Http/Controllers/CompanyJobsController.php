<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\JobPhase;
use App\Events\InterviewSheduled;
use App\Http\Resources\CompanyJobsResource;
use App\Http\Resources\CompanyStatsResource;
use App\Models\Job;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Auth\Access\AuthorizationException;
use App\Pipelines\Filters\JobFilters\SearchApplicants;
use App\Http\Resources\CompanyJobApplicationResource;
use App\Models\Application;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

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
            )
        ];
    }

    public function applicants(Job $job)
    {
        $this->authorize($job);

        $query = $job->applicants()->select('first_name', 'last_name', 'email', 'applications.*')->getQuery();
        $applicants = Pipeline::send($query)->through(SearchApplicants::class)->thenReturn()->paginate(10);

        return CompanyJobApplicationResource::collection($applicants)->additional([
            'jobPhase' =>  $job->phase,
        ]);
    }

    public function setMinScore(Request $request, Job $job)
    {
        $this->authorize($job);

        if ($job->phase != JobPhase::Revision) {
            return $this->error("Some CVs haven't been filtered yet!", 409);
        }

        $minCVScore = $request->validate(['min_score' => 'required|decimal:0,2|min:1|max:100'])['min_score'];

        $acceptedApplications = Application::whereJobId($job->id)->where('cv_score', '>=', $minCVScore);
        $acceptedApplications->update(['status' => ApplicationStatus::CVEligible]);
        Application::whereJobId($job->id)->where('cv_score', '<', $minCVScore)->update(['status' => ApplicationStatus::CVRejected]);

        // $job->update(['phase' => JobPhase::Interview]);
        $this->NotifyUsers($job);
        return $this->ok('Interview phase has started.');   
    }

    public function authorize(Job $job)
    {
        if ($job->company_id != auth()->id()) {
            throw new AuthorizationException;
        }
    }

    private function NotifyUsers($job)
    {
       // Get all applicants who are eligible for the interview
        $applicants = $job->applicants()
            ->wherePivot('status', ApplicationStatus::CVEligible)
            ->get();
        foreach($applicants as $applicant) {
            broadcast(new InterviewSheduled($applicant->id));
        }
    }

}
