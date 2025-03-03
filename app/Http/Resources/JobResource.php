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
            "type" => "job",
            "id" => $this->id,
            "attributes" => [
                "title" => $this->title,
                "type" => $this->type,
                "experienceLevel" => $this->experience_level,
                "workLocation" => $this->work_location,
                "isAvailable" => $this->is_available,
                "availableTo" => $this->available_to,
                "maxApplicants" => $this->maxApplicants,
                "companyLogo" => $this->company->logo,
                "salary" => $this->salary,
                "currency" => $this->currency,
                "summary" => $this->summary,
                $this->mergeWhen(!$request->routeIs(["jobs.index"]), [
                    "requirements" => $this->requirements,
                    "skills" => $this->skills,
                    "responsibilities" => $this->responsibilities,
                    "workHours" => $this->work_hours,
                ]),
                "createdAt" => $this->created_at,
                "updatedAt" => $this->updated_at,
            ],
            "links" => [
                "self" => route("jobs.show", ["job" => $this->id]),
            ],
            "relationships" => [
                "company" => [
                    "data" => [
                        "type" => "company",
                        "id" => $this->company_id,
                    ],
                    "links" => [
                        "self" => route("companies.show", [
                            "company" => $this->company_id,
                        ]),
                    ],
                ],
            ],

            "includes" => [CompanyResource::make($this->whenLoaded("company"))],
        ];
    }
}
