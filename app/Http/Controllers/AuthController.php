<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\RegisterApplicantRequest;
use App\Models\Applicant;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterApplicantRequest $request)
    {
        $validatedData = $request->validated();
        if (!$validatedData) {
            return response()->json("Invalid credentials");
        }
        $applicant = Applicant::create($validatedData);
        return $applicant;
    }
}
