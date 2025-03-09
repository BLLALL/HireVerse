<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerificationController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function verify(Request $request, $type, $id)
    {
        if (! $request->hasValidSignature()) {
            return $this->unauthorized('You are not authorized');
        }

        if (! in_array($type, ['applicant', 'company'])) {
            throw new NotFoundHttpException;
        }
        $model = 'App\\Models\\'.ucwords($type);

        $user = $model::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 409);
        }

        $user->markEmailAsVerified();

        return $this->ok(
            'Email verified successfully',
            [
                $type => $user,
                'token' => $this->generateToken($user),
            ]
        );
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 409);
        }
        event(new Registered($user));

        return $this->ok('Email verification link sent');
    }
}
