<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCompanyRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'ceo' => 'required|string|max:50',
            'email' => 'required|email|unique:companies,email',
            'password' => 'required|string|min:8',
            'location' => 'required|string|max:255',
            'employee_no' => 'required|integer|min:1|max:1000000',
            'website_url' => 'required|url:http,https',
            'description' => 'nullable|string',
            'insights' => 'nullable|string',
        ];
    }
}
