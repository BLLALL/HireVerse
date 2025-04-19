<?php

namespace App\AIServices;

use App\Enums\ApplicationStatus;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CVFiltration
{
    protected $requestData = [];

    public function __construct(protected Collection $applications)
    {
        foreach ($this->applications as $application) {

            $cvPath = $application->cv;
            $fileName = substr($cvPath, strrpos($cvPath, '/') + 1);

            $this->requestData[] = [
                'name'     => 'cvFiles',
                'contents' => fopen(public_path('storage/' . $application->cv), 'r'),
                'filename' => $fileName,
            ];
        }

        $job = Job::find($applications[0]->job_id);
        $this->requestData[] = [
            'name'     => 'jobDescription',
            'contents' => JobResource::make($job)->toJson(),
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

            for ($i = 0; $i < count($this->applications); $i++) {
                $this->applications[$i]->cv_score = (float) $cvScores[$i];
                $this->applications[$i]->save();
            }

            $this->applications->toQuery()->update(['status' => ApplicationStatus::CVProcessed]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function handle(Collection $applications)
    {
        new self($applications)->process();
    }
}
