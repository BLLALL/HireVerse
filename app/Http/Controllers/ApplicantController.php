<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicantResource;
use App\Models\Applicant;
use App\Traits\ApiResponses;

class ApplicantController extends Controller
{
    use ApiResponses;

    public function index(): mixed
    {
        return ApplicantResource::collection(Applicant::latest()->paginate(10));
    }

    public function show(Applicant $applicant): mixed
    {
        return ApplicantResource::make($applicant);
    }
}
