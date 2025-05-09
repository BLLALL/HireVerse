<?php

use App\Enums\ApplicationStatus;
use App\Events\ApplicantApplied;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\VerificationController;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/api_applicant.php';
require_once __DIR__ . '/api_company.php';

Route::post('/github-webhook', function (\Illuminate\Http\Request $request) {
    $secret = env('GITHUB_SECRET');
    $signature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

    if (!hash_equals($signature, $request->header('X-Hub-Signature-256'))) {
        \Log::warning('Webhook Signature Mismatch!');
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    \Log::info('Webhook received and verified.');
    $process = new Process(['git', 'pull']);
    $process->run();

    if (!$process->isSuccessful()) {
        \Log::error('Git Pull Error: ' . $process->getErrorOutput());
        return response()->json(['message' => 'Git pull failed!'], 500);
    }

    return response()->json(['message' => 'Git pull executed successfully!']);
});




Route::controller(VerificationController::class)->group(function () {
    Route::get('{type}/email/verify/{id}', 'verify')->name('verification.verify');
    Route::post('email/resend', 'resend')->middleware(['auth:sanctum', 'throttle:6,1', 'abilities:email-verification'])->name('verification.resend');
});

Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);
Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
Route::apiResource('applicants', ApplicantController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'ability:*', 'verified'])->group(function () {
    Route::get('auth/user', CurrentUserController::class);
});

Route::get('storage/{filePath}', function ($filePath) {
    if (! Storage::exists($filePath)) {
        return response()->json(['message' => 'File not found.'], 404);
    }
    return response()->file(public_path('storage/' . $filePath));
})->where('filePath', '.*');


Route::get('test', function () {

    $job = Job::find(1);
    
    Application::whereJobId($job->id)->whereIn('status', [ApplicationStatus::CVProcessing, ApplicationStatus::CVProcessed])->update([
        'status' => ApplicationStatus::Pending,
        'cv_score' => null
    ]);
    
    ApplicantApplied::dispatch($job);

    return 'HireVerse - HierServe';
});
