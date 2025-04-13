<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateApplicantPasswordRequest;
use App\Http\Requests\UpdateApplicantProfileRequest;
use App\Http\Resources\ApplicantResource;
use App\Models\Applicant;
use App\Traits\ApiResponses;
use App\Traits\FileHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantProfileController extends Controller
{
    use ApiResponses, FileHelpers;

    public function update(UpdateApplicantProfileRequest $request)
    {
        $attributes = $request->validated();
        $applicant = $request->user();

        if ($request->hasFile('cv')) {
            if ($applicant->cv) {
                Storage::delete($applicant->cv);
            }
            
            $cvFile = $request->file('cv');
            $attributes['cv'] = $cvFile->storeAs('applicants/cvs', $this->generateUniqueName($cvFile));
        }

        if ($request->hasFile('avatar')) {
            if ($applicant->avatar) {
                Storage::delete($applicant->avatar);
            }
            $attributes['avatar'] = $request->file('avatar')->store('applicants/avatars');
        }

        $applicant = tap($applicant, function (Applicant $applicant) use ($attributes) {
            $applicant->skills = $attributes['skills'] ?? null;
            $applicant->update($attributes);
        });

        return $this->ok('Profile updated.', ['applicant' => ApplicantResource::make($applicant)]);
    }

    public function changePassword(UpdateApplicantPasswordRequest $request)
    {
        $applicant = $request->user();
        $applicant->password = $request->password;
        $applicant->save();
        $applicant->currentAccessToken()->delete();

        return $this->ok('Password changed, please login again!');
    }

    public function deleteAccount()
    {
        $applicant = Auth::user();
        if ($applicant->cv) {
            Storage::delete($applicant->cv);
        }
        if ($applicant->avatar) {
            Storage::delete($applicant->avatar);
        }
        $applicant->delete();
        $applicant->currentAccessToken()->delete();

        return $this->ok('Account deleted successfully.');
    }
}
