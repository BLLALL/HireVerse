<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyJobsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $createdAt = $this->created_at->diffForHumans();
        $availableTo = Carbon::make($this->available_to)?->diffForHumans();
        $duration = floor($this->created_at->diffInDays($this->available_to));

        return [
            'type' => 'companyJobs',
            'jobId' => $this->id,
            'attributes' => [
                'jobTitle' => $this->title,
                'companyName' => $this->company->name,
                'createdAt' => $createdAt,
                'availableTo' => $availableTo,
                'applicantsCount' => $this->applicants()->count(),
                'worLdLocation' => $this->work_location,
                'jobLocation' => $this->job_location,
                'jobType' => $this->type,
                'duration' => $duration.' days',
            ],
        ];
    }
}
