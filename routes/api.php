<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\VerificationController;
use App\Models\Applicant;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('complete', 'complete')->middleware(['auth:sanctum']);
    Route::post('login', 'login');
});

Route::prefix('oauth')->controller(OAuthController::class)->group(function () {
    Route::get('{provider}/redirect', 'redirect');
    Route::get('{provider}/callback', 'callback');
    Route::post('{provider}/complete', 'complete');
});

Route::prefix('company')->controller(CompanyAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::controller(VerificationController::class)->group(function () {
    Route::get('{type}/email/verify/{id}', 'verify')->name('verification.verify');
    Route::post('email/resend', 'resend')->middleware(['auth:sanctum', 'throttle:6,1', 'abilities:email-verification'])->name('verification.resend');
});

Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);
Route::apiResource('jobs', JobController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum', 'ability:*')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('jobs/apply', [JobController::class, 'apply']);

    Route::get('users', function () {
        return Applicant::all();
    });
});

Route::get('test', fn () => 'HireVerse - HierServe');
