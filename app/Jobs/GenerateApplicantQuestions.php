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

class GenerateApplicantQuestions implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Job $j, protected Application $application) {}

    
    public function handle(QuestionsGenerationService $generator): void
    {
        $questions = $generator->generateQuestions(job: $this->j, questionsPerSkill: 3);

        // create interview record with the application id
        // insert the generated questions into questions table with the interview id
        
        $interview = Interview::create([
            'application_id' => $this->application->id,
        ]);

        $questions = array_map(function ($question) use ($interview) {
            $question['interview_id'] = $interview->id;
            return $question;
        }, $questions);

        Question::insert($questions);

        Log::info("Generated ". count($questions) . " questions for applicant ". $this->application->applicant_id);// 21 
    }
}
