<?php

use App\Models\Job;
use App\Models\Application;
use App\Events\InterviewPhaseStarted;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Jobs\GenerateApplicantQuestions;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ApplicantJobsController;
use App\Http\Controllers\ApplicantProfileController;
use App\Http\Controllers\technicalInterviewController;

Route::middleware(['auth:sanctum', 'ability:*', 'verified', 'can:applicant'])->group(function () {
    Broadcast::routes();

    Route::get('user', function () {
        return response()->json(auth()->user());
    });
    
    Route::prefix('applicant')->group(function () {
        Route::delete('/', [ApplicantProfileController::class, 'deleteAccount']);
        Route::get('jobs', [ApplicantJobsController::class, 'index']);
        Route::post('jobs/{job}/applications', [ApplicantJobsController::class, 'store']);
        Route::patch('profile', [ApplicantProfileController::class, 'update']);
        Route::patch('password', [ApplicantProfileController::class, 'changePassword']);
    });
    Route::prefix('interviews')->group(function () {
        Route::get('questions/jobs/{id}', function ($jobId) {
            // Dispatch the job to generate questions for the applicant
            $job = Job::findOrFail($jobId);
            // $application = Application::where('job_id', $jobId)
            //     ->where('applicant_id', Auth::user()->id)
            //     ->firstOrFail();
            return InterviewPhaseStarted::dispatch($job);
        });
        Route::post('answer', [technicalInterviewController::class, 'store']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('api_guest')->group(function () {
    Route::prefix('oauth')->controller(OAuthController::class)->group(function () {
        Route::get('{provider}/redirect', 'redirect');
        Route::get('{provider}/callback', 'callback');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });

    
});
