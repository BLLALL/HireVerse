<?php

namespace App\Http\Controllers;

use App\Http\Requests\{LoginCompanyRequest, RegisterCompanyRequest};
use App\Traits\{ApiResponses, TokenHelpers};
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Auth\Events\Registered;
use App\Models\Company;

class CompanyAuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function register(RegisterCompanyRequest $request) {

        $company = Company::create($request->validated());
        event(new Registered($company));

        return $this->success(
            'Company successfully created, please verify your email',
            ['verificationToken' => $this->generateEmailVerificationToken($company)],
            201
        );
    }

    public function login(LoginCompanyRequest $request)
    {
        $company = Company::firstWhere('email', $request->email);

        if (! Hash::check($request->password, $company->password)) {
            return $this->unauthorized('Invalid credentials!');
        }

        return $this->ok('Authenticated', ['company' => $company, 'token' => $this->generateToken($company)]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
