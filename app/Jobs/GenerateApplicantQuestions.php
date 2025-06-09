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

        //  hardcoded questions for testing
        // $questions = [
        //     [
        //     'question' => 'What is your experience with Laravel?',
        //     'difficulty' => QuestionDifficulty::Easy,
        //     'expected_keywords' => 'Eloquent, Blade, Middleware',
        //     'assessment_criteria' => 'Look for understanding of Eloquent ORM, Blade templating, and Middleware usage.'
        //     ],
        //     [
        //     'question' => 'How do you optimize database queries?',
        //     'difficulty' => QuestionDifficulty::Medium,
        //     'expected_keywords' => 'Indexes, Query Caching, Eager Loading',
        //     'assessment_criteria' => 'Check for knowledge of database optimization techniques like indexing, query caching, and eager loading.'
        //     ],
        //     [
        //     'question' => 'How do you handle version control in your projects?',
        //     'difficulty' => QuestionDifficulty::Medium,
        //     'expected_keywords' => 'Git, Branching, Merging',
        //     'assessment_criteria' => 'Look for familiarity with Git commands, branching strategies, and merging processes.'
        //     ],
        //     [
        //     'question' => 'Can you explain the MVC architecture?',
        //     'difficulty' => QuestionDifficulty::Easy,
        //     'expected_keywords' => 'Model, View, Controller',
        //     'assessment_criteria' => 'Assess understanding of the Model-View-Controller architecture and its components.'
        //     ],
        //     [
        //     'question' => 'What is your approach to debugging?',
        //     'difficulty' => QuestionDifficulty::Medium,
        //     'expected_keywords' => 'Debugging Tools, Logging, Error Handling',
        //     'assessment_criteria' => 'Look for knowledge of debugging tools, logging practices, and error handling strategies.'
        //     ],

        //     [
        //     'question' => 'How do you ensure code quality?',
        //     'difficulty' => QuestionDifficulty::Medium,
        //     'expected_keywords' => 'Code Reviews, Testing, Linting',
        //     'assessment_criteria' => 'Check for practices like code reviews, unit testing, and linting to ensure code quality.'
        //     ],
        // ];

        // dd($questions);
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
