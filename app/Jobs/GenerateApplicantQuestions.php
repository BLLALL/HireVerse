<?php

namespace App\Jobs;

use App\Models\Job;
use App\Models\Question;
use App\Models\Interview;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\Log;
use App\Notifications\InterviewScheduled;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\AIServices\QuestionsGenerationService;

class GenerateApplicantQuestions implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Job $j, protected Application $application) {}

    
    public function handle(QuestionsGenerationService $generator): void
    {
        $questions = $generator->generateQuestions(job: $this->j, questionsPerSkill: 3);
       
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
        
        $interview = Interview::firstOrCreate([
            'application_id' => $this->application->id,
            'deadline' =>  now()->addDays(3), // next 3 days from now
        ]);

        $this->application->status = ApplicationStatus::InterviewScheduled;
        $this->application->save();
            $questions = array_map(function ($question) use ($interview) {
            $question['interview_id'] = $interview->id;
            return $question;
        }, $questions);

        Question::insert($questions);

        $this->NotifyUsers($this->j, $interview->deadline);

    }

    private function NotifyUsers(Job $job, $interviewDeadline)
    {

            $applicant = $this->application->applicant;
            $applicant->notify(new InterviewScheduled( $interviewDeadline));
            Log::info("Interview scheduled notification sent to applicant: {$applicant->id} for job: {$job->id}");
        
    }
}
