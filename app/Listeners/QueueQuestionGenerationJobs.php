<?php

namespace App\Listeners;

use App\AIServices\QuestionsGeneration;
use App\Enums\ApplicationStatus;
use App\Events\InterviewPhaseStarted;
use App\Jobs\GenerateApplicantQuestions;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class QueueQuestionGenerationJobs implements ShouldQueue
{
    public function viaQueue(): string
    {
        return 'listeners';
    }

    public function __construct() {}

    public function handle(InterviewPhaseStarted $event): void
    {
        $job = $event->job;
        $applications = Application::whereJobId($job->id)->whereStatus(ApplicationStatus::CVEligible)->get();

        foreach($applications as $application) {
            GenerateApplicantQuestions::dispatch($job, $application)->delay(15)->onQueue('ai');
        }
    }
}
