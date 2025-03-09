<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginApplicantRequest;
use App\Http\Requests\RegisterApplicantRequest;
use App\Models\Applicant;
use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function register(RegisterApplicantRequest $request)
    {
        $applicant = Applicant::create($request->validated());

        event(new Registered($applicant));

        $verificationToken = $applicant->createToken('email-verification', ['email-verification'], now()->addHours(3))->plainTextToken;

        return $this->success(
            'Applicant successfully created, please verify your email',
            ['verificationToken' => $verificationToken],
            201
        );
    }

    public function login(LoginApplicantRequest $request)
    {
        $request->validated();
        $applicant = Applicant::firstWhere('email', $request->email);

        if (! auth()->guard('web')->attempt($request->only(['email', 'password']))) {
            return $this->unauthorized('Invalid credentials!');
        }

        $token = $applicant->createToken('API token for '.$applicant->email, ['*'], now()->addMonth())->plainTextToken;

        return $this->ok('Authenticated', ['applicant' => $applicant, 'token' => $token]);

    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
