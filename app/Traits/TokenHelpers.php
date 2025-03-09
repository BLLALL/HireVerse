<?php

namespace App\traits;

use App\Models\Applicant;
use App\Models\Company;

trait TokenHelpers
{
    protected function generateToken(Applicant|Company $user, $name = null, $abilities = ['*'], $to = null)
    {
        return $user->createToken(
            $name ?? "Token for {$user->email}",
            $abilities,
            $to ?? now()->addMonth()
        )->plainTextToken;
    }

    protected function generateEmailVerificationToken(Applicant|Company $user, $to = null)
    {
        return $user->createToken(
            "Email verification token for {$user->email}",
            ['email-verification'],
            $to ?? now()->addHours(3)
        )->plainTextToken;
    }
}
