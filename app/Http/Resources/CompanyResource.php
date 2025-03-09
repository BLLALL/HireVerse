<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'company',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'logoUrl' => $this->logo,
                'location' => $this->location,

                $this->mergeWhen(
                    ! $request->routeIs(['companies.index']),
                    [
                        'businessEmail' => $this->business_email,
                        'websiteUrl' => $this->website_url,
                        'ceo' => $this->ceo,
                        'description' => $this->description,
                        'insights' => $this->insights,
                        'industry' => $this->industry,
                        'employeeNumber' => $this->employee_no,
                    ]
                ),

                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'links' => [
                'self' => route('companies.show', ['company' => $this->id]),
            ],

            'includes' => [JobResource::collection($this->whenLoaded('jobs'))],
        ];
    }
}
