<?php

namespace App\Http\Requests;

use App\Enums\ExperienceLevel;
use App\Enums\JobType;
use App\Enums\WorkingHours;
use App\Enums\WorkLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', Rule::in(JobType::values())],
            'experience_level' => [
                'sometimes',
                'string',
                Rule::in(ExperienceLevel::values()),
            ],
            'summary' => ['nullable', 'string', 'max:510'],
            'salary' => ['nullable', 'integer', 'min:1', 'max:999999999'],
            'currency' => [
                'required_with:salary',
                'string',
                'max:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'work_hours' => [
                'sometimes',
                'string',
                Rule::in(WorkingHours::values()),
            ],
            'work_location' => [
                'sometimes',
                'string',
                Rule::in(WorkLocation::values()),
            ],
            'job_location' => ['sometimes', 'string', 'max:255'],
            'requirements' => ['sometimes', 'string', 'max:1200', 'min:10'],
            'responsibilities' => ['sometimes', 'string', 'max:1200', 'min:10'],
            'is_available' => ['nullable', 'boolean'],
            'available_to' => ['nullable', 'date', 'after:now'],
            'max_applicants' => ['nullable', 'integer', 'min:1'],
            'skills' => ['sometimes', 'array', 'min:1', 'max:20'],
            'skills.*' => ['required_with:skills', 'string', 'max:255'],
            'required_no_of_hires' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
