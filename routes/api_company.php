<?php

use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:*', 'verified', 'can:company'])->group(function () {
    Route::post('jobs', [JobController::class, 'store']);
    Route::post('company/logout', [CompanyAuthController::class, 'logout']);
});

Route::middleware('api_guest')->group(function () {
    Route::prefix('company')->controller(CompanyAuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
});
