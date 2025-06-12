<?php

namespace App\Http\Resources;

use App\Enums\ApplicationStatus;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CompanyStatsResource extends JsonResource
{
    public function toArray($request)
    {
        $acceptedApp = DB::table('applications')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->where('jobs.company_id', $this->id)
            ->where('applications.status', ApplicationStatus::Accepted);

        // Total counts
        $totalJobs = $this->jobs()->count();
        $totalAccepted = $acceptedApp->count();
        $totalApplications = DB::table('applications')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->where('jobs.company_id', $this->id)
            ->count();

        // Monthly changes using DB facade for more precise counting
        $jobsThisMonth = $this->jobs()->whereMonth('created_at', now()->month)->count();
        $jobsLastMonth = $this->jobs()->whereMonth('created_at', now()->subMonth()->month)->count();

        $acceptedThisMonth = (clone $acceptedApp)
            ->whereMonth('applications.created_at', now()->month)
            ->count();

        $acceptedLastMonth = (clone $acceptedApp)
            ->whereMonth('applications.created_at', now()->subMonth()->month)
            ->count();

        $applicationsThisMonth = DB::table('applications')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->where('jobs.company_id', $this->id)
            ->whereMonth('applications.created_at', now()->month)
            ->count();

        $applicationsLastMonth = DB::table('applications')
            ->join('jobs', 'applications.job_id', '=', 'jobs.id')
            ->where('jobs.company_id', $this->id)
            ->whereMonth('applications.created_at', now()->subMonth()->month)
            ->count();

        return [
            'publishedJobs' => [
                'total' => $totalJobs,
                'change' => $this->calculateChange($jobsThisMonth, $jobsLastMonth),
            ],
            'acceptedCandidates' => [
                'total' => $totalAccepted,
                'change' => $this->calculateChange($acceptedThisMonth, $acceptedLastMonth),
            ],
            'totalApplications' => [
                'total' => $totalApplications,
                'change' => $this->calculateChange($applicationsThisMonth, $applicationsLastMonth),
            ],
        ];
    }

    private function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? "+$current" : '0';
        }
        $change = $current - $previous;

        return $change >= 0 ? "+$change" : "$change";
    }
}
