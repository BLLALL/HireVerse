<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(): mixed
    {
        return CompanyResource::collection(Company::latest()->paginate(10));
    }

    public function show(Company $company): mixed
    {
        return CompanyResource::make($company);
    }
}
