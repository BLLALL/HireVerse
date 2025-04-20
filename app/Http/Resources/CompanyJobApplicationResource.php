<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyJobApplicationResource extends JsonResource
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
            'applicantId' => $this->applicant_id,
            'attributes' => [
                'applicantName' => $this->first_name . (empty($this->last_name) ? "" : " {$this->last_name}") ,
                'applicantEmail' => $this->email,
                'appliedAt' => Carbon::make($this->created_at)->format('M. j, Y, g:i a'),
                'status' => $this->status,
                'cv' => $this->cv,
                'cvScore' => $this->cv_score,
            ],
            'links' => [
                'applicant' => route('applicants.show', ['applicant' => $this->applicant_id]),
            ],
        ];
    }
}
