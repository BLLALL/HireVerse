<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
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
            $attributes['logo'] = $request->file('logo')->store('companies/logos');

        }

        $company->update($attributes);
        return response()->json([
            'message' => 'Company updated successfully',
            'data' => new CompanyResource($company->fresh())
        ]);
    }

    public function destroy(Company $company): mixed
    {
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully']);
    }
}
