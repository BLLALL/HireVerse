<?php

namespace App\Http\Resources;

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
                'isAvailable' => $this->is_available,

                $this->mergeWhen(
                    !$request->routeIs(['jobs.index']),
                    [
                        'summary' => $this->summary,
                        'salary' => $this->salary,
                        'currency' => $this->currency,
                        'workHours' => $this->work_hours,
                        'requirements' => $this->requirements,
                        'responsibilities' => $this->responsibilities,
                    ]
                ),

                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('jobs.show', ['job' => $this->id]),
            ],
            'relationships' => [
                'company' => [
                    'data' => [
                        'type' => 'company',
                        'id' => $this->company_id
                    ],
                    'links' => ['self' => route('companies.show', ['company' => $this->company_id])]
                ]
            ],

            'includes' => [new CompanyResource($this->whenLoaded('company'))],
        ];
    }
}
