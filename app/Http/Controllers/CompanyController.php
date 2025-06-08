<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\FileHelpers;
use App\Traits\ApiResponses;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\UpdateApplicantPasswordRequest;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use FileHelpers, ApiResponses;
    public function index(): mixed
    {
        return CompanyResource::collection(Company::latest()->get());
    }

    public function show(Company $company): mixed
    {
        return CompanyResource::make($company);
    }

    public function update(UpdateCompanyRequest $request): mixed
    {
        $attributes = $request->validated();
        $company = $request->user();
        
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete($company->logo);
            }
            $logoFile = $request->file('logo');
            $attributes['logo'] = $logoFile->storeAs('companies/logos', 
            $this->generateUniqueName($logoFile));

        }
        
        $company->update($attributes);
        return response()->json([
            'message' => 'Company updated successfully',
            'data' => new CompanyResource($company->fresh())
        ]);
    }


    public function changePassword(UpdateApplicantPasswordRequest $request)
    {
        $token = $request->user()->currentAccessToken();
        dd([
            'token_name' => $token->name,
            'token_provider' => get_class($request->user())
        ]);
        
        $company = $request->user();
        $company->password =    $request->password;
        $company->save();
        $company->currentAccessToken()->delete();

        return $this->ok('Password changed, please login again!');
    }

    public function destroy(Company $company): mixed
    {
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully']);
    }
}
