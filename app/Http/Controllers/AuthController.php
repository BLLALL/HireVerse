<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\RegisterApplicantRequest;
use App\Http\Requests\LoginAplicantRequest;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterApplicantRequest $request)
    {
        $validatedData = $request->validated();
        $applicant = Applicant::create($request->validated());

        event(new Registered($applicant));
        $applicant->sendEmailVerificationNotification();

        $verificationToken = $applicant->createToken(
            "email-verification",
            ["email-verification"],
            now()->addHours(3)
        )->plainTextToken;
        return response()->json(
            [
                "status" => 201,
                "message" =>
                    "user successfully created, please verify your email",
                "verification_token" => $verificationToken,
            ],
            201
        );
    }

    public function login(LoginAplicantRequest $request)
    {
        $request->validated();
        if (
            !auth()->guard("web")->attempt($request->only("email", "password"))
        ) {
            return response()->json(
                [
                    "status" => 401,
                    "message" => "Invalid credentials",
                ],
                401
            );
        }

        $applicant = Applicant::firstWhere("email", $request->email);
        $token = $applicant->createToken(
            "API token for " . $applicant->email,
            ["*"],
            now()->addMonth()
        )->plainTextToken;

        return response()->json(
            [
                "status" => 200,
                "message" => "authenticated",
                "data" => [
                    "user" => $applicant,
                    "token" => $token,
                ],
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
