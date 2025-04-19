<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:50',
            'ceo' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|email|unique:companies,email',
            'password' => 'sometimes|required|string|min:8',
            'location' => 'sometimes|required|string|max:255',
            'employee_no' => 'sometimes|required|integer|min:1|max:1000000',
            'website_url' => 'sometimes|required|url:http,https',
            'description' => 'sometimes|nullable|string',
            'insights' => 'sometimes|nullable|string',
            'industry' => 'sometimes|nullable|string|max:50',
            'logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
