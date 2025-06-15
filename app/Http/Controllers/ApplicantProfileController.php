<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateApplicantPasswordRequest;
use App\Http\Requests\UpdateApplicantProfileRequest;
use App\Http\Resources\ApplicantResource;
use App\Models\Applicant;
use App\Traits\ApiResponses;
use App\Traits\FileHelpers;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantProfileController extends Controller
{
    use ApiResponses, FileHelpers;

    public function notifications()
    {
        $applicant = Auth::user();

        return response()->json([
            'notifications' => $applicant->notifications->map(fn ($n) => [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'deadline' => $n->data['deadline'] ?? null,
                'interview_id' => $n->data['interview_id'] ?? null,
                'created_at' => $n->created_at,
                'is_read' => $n->read_at !== null,
            ]),
        ]);
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->noContent();
    }

    public function deleteNotification(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->id()) {
            return $this->error('You are not authorized to delete this notification.', 403);
        }

        $notification->delete();

        return $this->success('Notification deleted successfully.');
    }

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
