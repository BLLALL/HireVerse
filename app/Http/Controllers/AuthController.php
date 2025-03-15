<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteRegistrationRequest;
use App\Http\Requests\LoginApplicantRequest;
use App\Http\Requests\RegisterApplicantRequest;
use App\Http\Resources\ApplicantResource;
use App\Models\Applicant;
use App\Models\Skill;
use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function register(RegisterApplicantRequest $request)
    {
        $applicant = Applicant::create($request->validated());

        $applicant->sendEmailVerificationNotification();

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

        $token = $applicant->createToken('API token for ' . $applicant->email, ['*'], now()->addMonth())->plainTextToken;

        return $this->ok('Authenticated', ['applicant' => $applicant, 'token' => $token]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function complete(CompleteRegistrationRequest $request)
    {
        $applicant = tap(Auth::user(), function (Applicant $applicant) use ($request) {
            $uniqueSkills = array_unique($request->skills);
            $skills = array_map(function ($skill) {
                return [
                    'title' => $skill,
                    'skillable_type' => 'App\\Models\\Applicant',
                    'skillable_id' => Auth::id(),
                ];
            }, $uniqueSkills);

            $applicant->skills()->delete();
            Skill::insert($skills);

            $applicant->update($request->only('job_title'));
        });

        return $this->ok('Applicant successfully completed registration.', ['applicant' => ApplicantResource::make($applicant)]);
    }
}
