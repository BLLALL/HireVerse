<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterApplicantRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:applicants,email', 'max:80'],
            'password' => ['required', 'string', 'min:8'],
            'job_title' => 'required|string|max:100',
            'skills' => 'sometimes|array|min:1|max:50',
            'skills.*' => 'string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'skills.*.string' => 'Skill item must be a string.',
            'skills.*.max' => 'Skill item must not be greater than 50 characters.',
        ];
    }
}
