<?php

namespace App\AIServices;

use App\Models\Applicant;
use App\Models\Job;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;


class Recommendation
{
    protected $requestData = [];

    public function __construct(protected Client $client) {}

    public function process()
    {
        try {
            $response = $this->client->get(config('app.ai_services_url') . '/recommendation', [
                'json' => $this->requestData,
            ]);

            $body = json_decode($response->getBody(), true);
            $recommendations = $body['recommendations'];
            return $recommendations;

        } catch (Exception $e) {
            return [['recommendedJobsIds' => []]];
        }
    }

    public function prepare(bool $allApplicants)
    {
        $query = Applicant::with('skills');
        
        if (! $allApplicants) {
            $query->whereId(Auth::id());
        }
        
        $applicants = $query->get('id')->map(fn($applicant) => [
            'id' => $applicant->id,
            'skills' => $applicant->skills->pluck('title')
        ]);
        
        $jobs = Job::available()->with('skills')->get('id')->map(fn($job) => [
            'id' => $job->id,
            'skills' => $job->skills->pluck('title')
        ]);
        

        $this->requestData = ['applicants' => $applicants, 'jobs' => $jobs];

        return $this;
    }


    public function handle(bool $allApplicants=false)
    {
        $recommendations = $this->prepare($allApplicants)->process();
        
        if (! $allApplicants) {
            return $recommendations[0]['recommendedJobsIds'];
        }

        return $recommendations;
    }
}
