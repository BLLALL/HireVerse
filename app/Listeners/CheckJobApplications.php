<?php

namespace App\Listeners;

use App\Enums\ApplicationStatus;
use App\Events\ApplicantApplied;
use App\Jobs\FilterCVs;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckJobApplications implements ShouldQueue
{
    public function viaQueue(): string
    {
        return 'listeners';
    }

    public function handle(ApplicantApplied $event): void
    {
        $job = $event->job;

        $query = Application::whereJobId($job->id);
        $applicationsCount = $query->count('id');
        $pendingApplications = $query->whereStatus(ApplicationStatus::Pending)->get();
        $maxApplicantsReached = $applicationsCount == $job->max_applicants;

        if ($maxApplicantsReached) {
            $job->update(['is_available' => false]);
        }

        if ($maxApplicantsReached || count($pendingApplications) >= 3) {
            $pendingApplications->toQuery()->update(['status' => ApplicationStatus::CVProcessing]);
            FilterCVs::dispatch($pendingApplications);
        }
    }
}
