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
                'availableTo' => $this->available_to ? Carbon::parse($this->available_to)->toIso8601String() : null,
                'maxApplicants' => $this->maxApplicants,
                'companyLogo' => $this->company->logo,
                'salary' => $this->salary,
                'currency' => $this->currency,
                'summary' => $this->summary,
                $this->mergeWhen(! $request->routeIs(['jobs.index']), [
                    'requirements' => $this->requirements,
                    'responsibilities' => $this->responsibilities,
                    'workHours' => $this->work_hours,
                    'skills' => $this->whenLoaded('skills', function() {
                        return $this->skills->pluck('title');
                    }),
                ]),
                'createdAt' => $this->created_at ? Carbon::parse($this->created_at)->toIso8601String() : null,
                'updatedAt' => $this->updated_at ? Carbon::parse($this->updated_at)->toIso8601String() : null,

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
