<?php

namespace App\Jobs;

use App\AIServices\QuestionsGenerationService;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Question;
use App\Notifications\InterviewScheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateApplicantQuestions implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Job $j, protected Application $application) {}

    public function handle(QuestionsGenerationService $generator): void
    {
        $generatedQuestions = $generator->generateQuestions(job: $this->j, questionsPerSkill: 3);

        $interview = $this->application->interview()->create();

        $questionsFilePath = "interviews/{$interview->id}/questions.json";
        Storage::put($questionsFilePath, json_encode($generatedQuestions, JSON_PRETTY_PRINT));

        $questions = array_map(function ($q) use ($interview) {
            return [
                'interview_id' => $interview->id,
                'question' => $q['question'],
                'difficulty' => $q['difficulty'],
            ];
        
        }, $generatedQuestions);

        Question::insert($questions);

        $this->NotifyUsers($this->j, $interview);

    }

    private function NotifyUsers(Job $job, $interview)
    {

        $applicant = $this->application->applicant;
        $applicant->notify(new InterviewScheduled($interview));
        Log::info("Interview scheduled notification sent to applicant: {$applicant->id} for job: {$job->id}");

    }
}
