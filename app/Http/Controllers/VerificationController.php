<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class VerificationController extends Controller
{
    public function verify($applicant_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(
                [
                    "status" => 401,
                    "message" => "You are not authorized",
                ],
                401
            );
        }
        $applicant = Applicant::findOrFail($applicant_id);

        if (!$applicant->hasVerifiedEmail()) {
            $applicant->markEmailAsVerified();
        }

        $token = $applicant->createToken(
            "API token for " . $applicant->email,
            ["*"],
            now()->addMonth()
        )->plainTextToken;

        return response()->json(
            [
                "status" => 200,
                "message" => "Email verified successfully",
                "data" => [
                    "user" => $applicant,
                    "token" => $token,
                ],
            ],
            200
        );
    }

    public function resend(Request $request)
    {
        $applicant = $request->user();
        if ($applicant->hasVerifiedEmail()) {
            return response()->json(
                [
                    "status" => 409,
                    "message" => "Email already verified",
                ],
                409
            );
        }
        event(new Registered($applicant));
        $applicant->sendEmailVerificationNotification();

        return response()->json(
            [
                "status" => 200,
                "message" => "Email verification link sent",
            ],
            200
        );
    }
}
