<?php

namespace App\Jobs;

use App\AIServices\QuestionsGenerationService;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateApplicantQuestions implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Job $j, protected Application $application) {}

    
    public function handle(QuestionsGenerationService $generator): void
    {
        $questions = $generator->generateQuestions(job: $this->j, questionsPerSkill: 3);
        dd($questions);
        // create interview record with the application id
        // insert the generated questions into questions table with the interview id
        $interview = $this->application->interviews()->create([
            'job_id' => $this->j->id,
            'status' => 'scheduled',
        ]);

        // Question::create([
        //     'interview_id' => $interview->id,
        //     'question' => $question,

        // ])
        Log::info("Generated ". count($questions) . " questions for applicant ". $this->application->applicant_id);// 21 
    }
}
