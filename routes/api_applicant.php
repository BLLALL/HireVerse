<?php

use App\Http\Controllers\ApplicantJobsController;
use App\Http\Controllers\ApplicantProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:*', 'verified', 'can:applicant'])->group(function () {
    Route::prefix('applicant')->group(function () {
        Route::get('jobs', [ApplicantJobsController::class, 'index']);
        Route::post('jobs/{job}/applications', [ApplicantJobsController::class, 'store']);
        Route::patch('profile', [ApplicantProfileController::class, 'update']);
        Route::patch('password', [ApplicantProfileController::class, 'changePassword']);
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
        Route::patch('complete', 'complete')->withoutMiddleware('api_guest')->middleware(['auth:sanctum']);
    });
});
