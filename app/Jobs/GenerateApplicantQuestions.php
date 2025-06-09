<?php

namespace App\Jobs;

use App\AIServices\QuestionsGenerationService;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Question;
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

        // create interview record with the application id
        // insert the generated questions into questions table with the interview id
        // store questions.json file in S3
        
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

        Log::info("Generated ". count($questions) . " questions for applicant ". $this->application->applicant_id);
    }
}
