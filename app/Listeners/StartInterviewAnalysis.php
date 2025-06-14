<?php

namespace App\Listeners;

use App\AIServices\InterviewAnalysisService;
use App\Events\ApplicantConductedInterview;
use App\Models\Question;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StartInterviewAnalysis implements ShouldQueue
{
    
    public function viaQueue(): string
    {
        return 'listeners';
    }

    public function __construct(protected InterviewAnalysisService $evaluator) {}


    public function handle(ApplicantConductedInterview $event): void
    {
        $interview = $event->interview;
        $this->evaluator->evaluateResponses($interview);
    }
}
