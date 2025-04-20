<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'jobApplication',
            'jobId' => $this->id,
            'attributes' => [
                'jobTitle' => $this->title,
                'companyName' => $this->company->name,
                'appliedAt' => Carbon::parse($this->applied_at)->format('M. j, Y, g:i a'),
                'status' => $this->status,
                'cv' => $this->cv,
                'cvScore' => $this->cv_score,
            ],
            'links' => [
                'job' => route('jobs.show', ['job' => $this->id]),
            ],
            'relationships' => [
                'company' => [
                    'data' => [
                        'type' => 'company',
                        'id' => $this->company_id,
                    ],
                    'links' => [
                        'self' => route('companies.show', [
                            'company' => $this->company_id,
                        ]),
                    ],
                ],

            ],
        ];
    }
}
