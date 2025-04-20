<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyJobsResource;
use App\Http\Resources\CompanyStatsResource;
use App\Models\Job;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Auth\Access\AuthorizationException;
use App\Pipelines\Filters\JobFilters\SearchApplicants;
use App\Http\Resources\CompanyJobApplicationResource;

class CompanyJobsController extends Controller
{
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
        $company = auth()->user();

        if ($job->company_id != $company->id) {
            throw new AuthorizationException;
        }

        $query = $job->applicants()->select('first_name', 'last_name', 'email', 'applications.*')->getQuery();
        $applicants = Pipeline::send($query)->through(SearchApplicants::class)->thenReturn()->paginate(10);

        return CompanyJobApplicationResource::collection($applicants);
    }
}
