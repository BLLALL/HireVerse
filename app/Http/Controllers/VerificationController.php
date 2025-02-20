<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($applicant_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(
                [
                    "message" => "You are not authorized.",
                ],
                401
            );
        }
        $applicant = Applicant::findOrFail($applicant_id);

        if (!$applicant->hasVerifiedEmail()) {
            $applicant->markEmailAsVerified();
        }

        $applicant["token"] = $applicant->createToken(
            "API token for " . $applicant->email,
            ["*"],
            now()->addDays(30)
        )->plainTextToken;

        return response()->json([
            "message" => "Email verified successfully",
            "data" => $applicant,
        ]);
    }

    public function resend()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return response()->json(
                [
                    "message" => "You are not authorized.",
                ],
                253
            );
        }
        auth()->user()->sendEmailVerificationNotification();
        return response()->json(
            [
                "message" => "Email verification link sent.",
            ],
            253
        );
    }
}
