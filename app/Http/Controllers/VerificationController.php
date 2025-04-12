<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
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
        $model = 'App\\Models\\' . ucwords($type);

        $user = $model::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 409);
        }
        
        $user->markEmailAsVerified();

        return redirect(config('app.frontend_url') . '/Login');
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 409);
        }

        $user->sendEmailVerificationNotification();

        return $this->ok('Email verification link sent');
    }
}
