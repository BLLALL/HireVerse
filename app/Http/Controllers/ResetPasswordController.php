<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:applicants,email']);
        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent']);
        } else {
            return response()->json(['message' => 'Error sending reset link', 'status' => $status], 500);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();
        if (! $resetRecord) {
            return response()->json([
                'message' => 'invalid or expired token',
            ], 400);
        }

        $applicant = Applicant::whereEmail($resetRecord->email)->first();

        if (! $applicant) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $applicant->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_resets')->where('email', $resetRecord->email)->delete();

        return response()->json(['message' => 'password reset successfully']);
    }
}
