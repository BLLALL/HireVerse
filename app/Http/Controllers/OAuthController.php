<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteOAuthRequest;
use App\Traits\{ApiResponses, TokenHelpers};
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;
use App\Models\Applicant;

class OAuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider)
    {
        $oAuthUser = Socialite::driver($provider)->stateless()->user();

        // determine wheather to sign up or sign in
        if (! $applicant = Applicant::firstWhere(["provider" => $provider, "provider_id" => $oAuthUser->id])) {
            return $this->createOAuthUser($oAuthUser, $provider);
        }

        // sign in
        return $this->ok(
            "Signed in with {$provider} successfully!",
            [
                "applicant" => $applicant,
                "token" => $this->generateToken($applicant),
            ]
        );
    }

    public function createOAuthUser($oAuthUser, $provider)
    {
        $fullName = explode(" ", $oAuthUser->name);

        $firstName = $fullName[0];
        $lastName = end($fullName) == $firstName ? null : end($fullName);

        $email = $oAuthUser->email;

        $attributes = [
            "provider" => $provider,
            "provider_id" => $oAuthUser->id,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "email" => $email,
        ];

        // if there is missing data such as first_name or last_name
        if (in_array(null, $attributes)) {
            return $this->ok("Please complete the missing data!", $attributes);
        }

        // if the user signed up previously with a provider and tries to sign in with another provider with the same email
        if (Applicant::whereEmail($email)->exists()) {
            return $this->error("Email has already been taken!", 409);
        }

        $applicant = Applicant::create($attributes);

        // mark email as verfied because the email is taken from google or github, so it is already verified
        $applicant->markEmailAsVerified();

        return $this->success(
            "Signed up with {$provider} successfully!",
            [
                "applicant" => $applicant,
                "token" => $this->generateToken($applicant),
            ],
            201
        );
    }

    public function complete(CompleteOAuthRequest $request)
    {
        $applicant = Applicant::create($request->validated());

        // here we need to verify email because the user might change it in this request
        event(new Registered($applicant));

        return $this->success(
            "Signed up with {$request->provider} successfully, please verify your email!",
            ['verificationToken' => $this->generateEmailVerificationToken($applicant)],
            201
        );
    }
}
