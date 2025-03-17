<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('api_guest')->group(function () {
    Route::prefix('oauth')
        ->controller(OAuthController::class)
        ->group(function () {
            Route::get('{provider}/redirect', 'redirect');
            Route::get('{provider}/callback', 'callback');
        });

    Route::controller(AuthController::class)->group(function () {
        Route::patch('complete', 'complete')
            ->withoutMiddleware('api_guest')
            ->middleware(['auth:sanctum']);
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    Route::prefix('company')
        ->controller(CompanyAuthController::class)
        ->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
        });
});

Route::controller(VerificationController::class)->group(function () {
    Route::get('{type}/email/verify/{id}', 'verify')->name(
        'verification.verify'
    );
    Route::post('email/resend', 'resend')
        ->middleware([
            'auth:sanctum',
            'throttle:6,1',
            'abilities:email-verification',
        ])
        ->name('verification.resend');
});

Route::apiResource('companies', CompanyController::class)->only([
    'index',
    'show',
]);
Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
Route::apiResource('applicants', ApplicantController::class)->only([
    'index',
    'show',
]);

Route::middleware(['auth:sanctum', 'ability:*'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('company/logout', [CompanyAuthController::class, 'logout']);
    Route::post('jobs', [JobController::class, 'store']);
    Route::post('jobs/apply', [JobController::class, 'apply']);
    Route::get('auth/user', CurrentUserController::class);
});

Route::get('test', fn () => 'HireVerse - HierServe');
