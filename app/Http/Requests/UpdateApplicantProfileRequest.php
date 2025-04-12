<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'cv' => 'nullable|file|mimes:pdf|max:10240',
            'job_title' => 'sometimes|required|string|max:100',
            'skills' => 'sometimes|array|min:1|max:50',
            'skills.*' => 'string|max:50',
            'github_url' => 'nullable|url:http,https',
            'linkedin_url' => 'nullable|url:http,https',
            'college' => 'nullable|string|max:80',
            'department' => 'nullable|string|max:80',
            'avatar' => 'nullable|file|mimes:png,jpg,jpeg|max:10240',
            'birthdate' => ['nullable', 'date', 'before:'.today()->subYears(15)],
        ];
    }

    public function messages()
    {
        return [
            'birthdate.before' => 'You must be at least 15 years old.',
            'skills.*.string' => 'Skill item must be a string.',
            'skills.*.max' => 'Skill item must not be greater than 50 characters.',
        ];
    }
}
