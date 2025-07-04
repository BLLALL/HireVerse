<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'applicant',
            'id' => $this->id,
            'attributes' => [
                'firstName' => $this->first_name,
                'lastName' => $this->last_name,
                'avatarUrl' => $this->avatar,
                'email' => $this->email,
                'jobTitle' => $this->job_title,
                'cvUrl' => $this->cv,

                $this->mergeWhen(
                    ! $request->routeIs(['applicants.index']),
                    fn () => [
                        'skills' => $this->skills_titles,
                        'college' => $this->college,
                        'department' => $this->department,
                        'birthdate' => $this->birthdate,
                        'githubUrl' => $this->github_url,
                        'linkedinUrl' => $this->linkedin_url,
                    ]
                ),

                'registered' => $this->created_at->diffForHumans(),
                'updated' => $this->updated_at->diffForHumans(),
            ],
            'links' => [
                'self' => route('applicants.show', ['applicant' => $this->id]),
            ],

            'includes' => JobResource::collection($this->whenLoaded('jobs')),
        ];
    }
}
