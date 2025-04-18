<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string|current_password:api',
            'password' => 'required|string|min:8|confirmed|different:old_password',
        ];
    }

    public function guard()
    {
        return $this->user()->guard_name ?? 'api';
    }
}
