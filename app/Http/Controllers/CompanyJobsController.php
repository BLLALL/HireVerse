<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\JobController;
use App\Http\Resources\CompanyJobsResource;
use App\Http\Resources\CompanyStatsResource;

class CompanyJobsController extends Controller
{
    public function index()
    {
        $company = Auth()->user();
        
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
}
