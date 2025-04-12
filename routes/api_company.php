<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\CompanyJobsController;


Route::middleware(['auth:sanctum', 'ability:*', 'verified', 'can:company'])->group(function () {
    Route::get('company/jobs', [CompanyJobsController::class, 'index']);
    Route::post('jobs', [JobController::class, 'store']);
    Route::post('company/logout', [CompanyAuthController::class, 'logout']);
});

Route::middleware('api_guest')->group(function () {
    Route::prefix('company')->controller(CompanyAuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
});
