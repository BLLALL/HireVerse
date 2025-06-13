<?php

namespace App\AIServices;

use App\Models\Applicant;
use App\Models\Job;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
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
            Log::error($e->getMessage());
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


    public function handle(bool $allApplicants = false)
    {
        if (! Auth::id()) {
            return [];
        }

        $key = "recommended_for_applicant_" . Auth::id();
        $recommendedJobsIds = Cache::get($key, []);

        if (count($recommendedJobsIds)) {
            return $recommendedJobsIds;
        }

        $recommendations = $this->prepare($allApplicants)->process();

        if (! $allApplicants) {
            $results = $recommendations[0]['recommendedJobsIds'];
            $this->cacheRecommendations($key, $results);
            return $results;
        }

        return $recommendations;
    }

    protected function cacheRecommendations($key, $jobsIds)
    {
        if (count($jobsIds)) {
            Cache::put($key, $jobsIds, now()->addHours(6));
        }
    }
}
