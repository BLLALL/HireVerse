<?php

namespace App\AIServices;

use App\Models\Interview;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\URL;

class InterviewAnalysisService
{
    
    protected $requestData = [];

    public function __construct(protected Client $client) {}

    public function process()
    {
        try {
            $this->client->post(config('app.ai_services_url') . '/interview/analysis', [
                'json' => $this->requestData,
            ]);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function prepare(Interview $interview)
    {
        $questionsPath = "interviews/{$interview->id}/questions.json";
        $answersPaths = $interview->questions()->orderBy('id')->pluck('applicant_answer');

        $callbackUrl = URL::temporarySignedRoute('interviews.analysis.callback', now()->addHours(24), ['interview' => $interview->id]);

        $this->requestData = [
            'interviewId' => $interview->id,
            'questionsPath' => $questionsPath,
            'answersPaths' => $answersPaths,
            'callbackUrl' => $callbackUrl,
        ];

        return $this;
    }

    
    public function evaluateAnswers(Interview $interview)
    {
        $this->prepare($interview)->process();
    }
   
}
