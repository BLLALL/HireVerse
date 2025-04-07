<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginApplicantRequest;
use App\Http\Requests\RegisterApplicantRequest;
use App\Http\Resources\ApplicantResource;
use App\Jobs\SendVerificationMail;
use App\Models\Applicant;
use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function register(RegisterApplicantRequest $request)
    {
        $applicant = Applicant::create($request->validated());

        SendVerificationMail::dispatch($applicant);

        $verificationToken = $applicant->createToken(
            'email-verification',
            ['email-verification'],
            now()->addHours(3)
        )->plainTextToken;

        $applicant->skills = $request->skills;

        return $this->success(
            'Applicant successfully created, please verify your email',
            [
                'applicant' => ApplicantResource::make($applicant),
                'verificationToken' => $verificationToken,
            ],
            201
        );
    }

    public function login(LoginApplicantRequest $request)
    {
        $request->validated();
        $applicant = Applicant::firstWhere('email', $request->email);

        if (
            ! auth()
                ->guard('web')
                ->attempt($request->only(['email', 'password']))
        ) {
            return $this->unauthorized('Invalid credentials!');
        }

        $token = $applicant->createToken(
            'API token for '.$applicant->email,
            ['*'],
            now()->addMonth()
        )->plainTextToken;

        return $this->ok('Authenticated', [
            'applicant' => ApplicantResource::make($applicant),
            'token' => $token,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
