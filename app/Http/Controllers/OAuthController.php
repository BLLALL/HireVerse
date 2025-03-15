<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

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

        if (! $applicant = Applicant::where(['provider' => $provider, 'provider_id' => $oAuthUser->id])->orWhere('email', $oAuthUser->email)->first()) {
            return $this->createOAuthUser($oAuthUser, $provider);
        }

        $token = $this->generateToken($applicant);
        return redirect()->away(config('app.frontend_url') . "/oauth/callback?token={$token}&status=200");
    }

    public function createOAuthUser(User $oAuthUser, string $provider)
    {
        $fullName = explode(' ', $oAuthUser->name ?? $oAuthUser->nickname);

        $firstName = $fullName[0];
        $lastName = count($fullName) < 2 ? null : end($fullName);
        $email = $oAuthUser->email;

        $attributes = [
            'provider' => $provider,
            'provider_id' => $oAuthUser->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'email_verified_at' => now(),
        ];

        $applicant = Applicant::create($attributes);
        $token = $this->generateToken($applicant);

        return redirect()->away(config('app.frontend_url') . "/oauth/callback?token={$token}&status=201");
    }
}
