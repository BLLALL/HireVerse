<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginCompanyRequest;
use App\Http\Requests\RegisterCompanyRequest;
use App\Models\Company;
use App\Traits\ApiResponses;
use App\Traits\TokenHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyAuthController extends Controller
{
    use ApiResponses, TokenHelpers;

    public function register(RegisterCompanyRequest $request)
    {
        $company = Company::create($request->validated());
        $company->sendEmailVerificationNotification();

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
