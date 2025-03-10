<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'type' => 'job',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'type' => $this->type,
                'experienceLevel' => $this->experience_level,
                'workLocation' => $this->work_location,
                'isAvailable' => $this->is_available,
                'availableTo' => $this->available_to->toDateString(),
                'maxApplicants' => $this->max_applicants,
                'companyLogo' => $this->company->logo,
                'salary' => $this->salary,
                'currency' => $this->currency,
                'summary' => $this->summary,
                $this->mergeWhen(! $request->routeIs(['jobs.index']), [
                    'requirements' => $this->requirements,
                    'responsibilities' => $this->responsibilities,
                    'workHours' => $this->work_hours,
                    'skills' => $this->skills,
                ]),
                'createdAt' => $this->created_at->diffForHumans(),
                'updatedAt' => $this->updated_at->diffForHumans(),

            ],
            'links' => [
                'self' => route('jobs.show', ['job' => $this->id]),
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

        if ($this->relationLoaded('company')) {
            $response['includes'] = [
                (new CompanyResource($this->company))->toArray($request),
            ];
        }

        return $response;
    }
}
