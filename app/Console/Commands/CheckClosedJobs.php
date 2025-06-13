<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Jobs\FilterCVs;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Console\Command;

class CheckClosedJobs extends Command
{
    protected $signature = 'app:check-closed-jobs';

    protected $description = 'Close the jobs that reached the close date and send all their pending CVs to AI service for filtration';

    public function handle()
    {
        $closedJobs = Job::where('available_to', '<', today()->toDateString());

        if ($closedJobs->count('id') == 0) {
            return;
        }

        $closedJobs->update(['is_available' => false]);

        $pendingApplications = Application::whereIn('job_id', $closedJobs->pluck('id'))->whereStatus(ApplicationStatus::Pending)->get();

        if ($pendingApplications->isEmpty()) {
            return;
        }

        $pendingApplications->toQuery()->update(['status' => ApplicationStatus::CVProcessing]);
        $pendingApplications->groupBy('job_id')->each(fn ($applications) => FilterCVs::dispatch($applications)->onQueue('ai'));
    }
}
