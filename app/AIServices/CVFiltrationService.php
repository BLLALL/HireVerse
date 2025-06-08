<?php

namespace App\AIServices;

use App\Enums\ApplicationStatus;
use App\Enums\JobPhase;
use App\Http\Resources\JobResource;
use App\Models\Application;
use App\Models\Job;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CVFiltrationService
{
    protected $requestData = [];
    protected $job;

    public function __construct(protected Collection $applications)
    {
        foreach ($this->applications as $application) {

            $cvPath = $application->cv;
            $fileName = substr($cvPath, strrpos($cvPath, '/') + 1);

            if (Storage::fileMissing($application->cv)) {
                throw new Exception("File \"{$application->cv}\" not found!");
            }

            $this->requestData[] = [
                'name'     => 'cvFiles',
                'contents' => fopen(Storage::path($application->cv), 'r'),
                'filename' => $fileName,
            ];
        }

        $this->job = Job::find($this->applications[0]->job_id);

        $this->requestData[] = [
            'name'     => 'jobDescription',
            'contents' => JobResource::make($this->job)->toJson(),
        ];
    }

    public function process()
    {
        try {
            DB::beginTransaction();

            $response = app(Client::class)->post(config('app.ai_services_url') . '/cv-filtration', [
                'multipart' => $this->requestData,
            ]);

            $body = json_decode($response->getBody(), true);
            $cvScores = $body['cvScores'];

            for ($i = 0; $i < $this->applications->count(); $i++) {
                $this->applications[$i]->cv_score = (float) $cvScores[$i];
                $this->applications[$i]->save();
            }

            $this->applications->toQuery()->update(['status' => ApplicationStatus::CVProcessed]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->handleJobPhase();
    }

    public function handleJobPhase()
    {
        if ($this->job->is_available) {
            return;
        }

        $allCVsProcessed = ! Application::whereJobId($this->job->id)->whereCvScore(null)->exists();

        if ($allCVsProcessed && $this->job->phase == JobPhase::CVFiltration) {
            $this->job->update(['phase' => JobPhase::Revision]);
        }
    }

    public static function handle(Collection $applications)
    {
        new self($applications)->process();
    }
}
