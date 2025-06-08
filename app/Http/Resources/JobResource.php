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
        return [
            'type' => 'job',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'type' => $this->type,
                'experienceLevel' => $this->experience_level,
                'workLocation' => $this->work_location,
                'jobLocation' => $this->job_location,
                'isAvailable' => $this->is_available,
                'availableTo' => Carbon::make($this->available_to)?->toDateString(),
                'maxApplicants' => $this->max_applicants,
                'companyLogo' => $this->company->logo,
                'companyName' => $this->company->name,
                'salary' => $this->salary,
                'currency' => $this->currency,
                'summary' => $this->summary,

                $this->mergeWhen(
                    ! $request->routeIs('jobs.index'),
                    fn() => [
                        'requirements' => $this->requirements,
                        'responsibilities' => $this->responsibilities,
                        'workHours' => $this->work_hours,
                        'skills' => $this->skills_titles,
                    ]
                ),

                'created' => $this->created_at->diffForHumans(),
                'updated' => $this->updated_at->diffForHumans(),

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
    }
}
