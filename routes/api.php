<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/api_applicant.php';
require_once __DIR__.'/api_company.php';

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

// Route::get('test', function () {

//     $a = Applicant::find(1);

//     return $a->jobs()->get();

//     return 'HireVerse - HierServe';
// });
