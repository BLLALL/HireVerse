<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:applicants,email',
        ]);

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);
        $applicant = Applicant::whereEmail($request->email);

        Mail::send('emails/reset_password.blade.php', [$applicant, $token],
            function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Your Password');
            });

        return response()->json(['message' => 'password reset instructions sent to your email']);
    }
}
