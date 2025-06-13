<?php

namespace App\Listeners;

use App\AIServices\InterviewAnalysisService;
use App\Enums\ApplicationStatus;
use App\Events\ApplicantConductedInterview;
use App\Models\Question;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnalyzeInterview implements ShouldQueue
{
    
    public function viaQueue(): string
    {
        return 'listeners';
    }

    public function __construct(protected InterviewAnalysisService $evaluator) {}


    public function handle(ApplicantConductedInterview $event): void
    {
        $interview = $event->interview;

        $interview->application->status = ApplicationStatus::Interviewed;
        $interview->application->save();
        

        $this->evaluator->evaluateResponses($interview);
    }
}
