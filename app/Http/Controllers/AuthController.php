<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\RegisterApplicantRequest;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(RegisterApplicantRequest $request)
    {
        $validatedData = $request->validated();
        if (!$validatedData) {
            return response()->json("Invalid credentials");
        }

        $applicant = Applicant::create($request->validated());

        $applicant->sendEmailVerificationNotification();
        event(new Registered($applicant));

        return response()->json([
            "message" => "User successfully created.",
        ]);
    }
}
