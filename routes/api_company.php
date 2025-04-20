<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\CompanyJobsController;


Route::middleware(['auth:sanctum', 'ability:*', 'verified', 'can:company'])->group(function () {
    Route::patch('companies', [CompanyController::class, 'update']);
    Route::patch('companies/password', [CompanyController::class, 'changePassword']);
    Route::delete('companies/{company}', [CompanyController::class, 'destroy']);

    Route::get('company/jobs', [CompanyJobsController::class, 'index']);
    Route::get('company/jobs/{job}/applicants', [CompanyJobsController::class, 'applicants']);
    
    Route::post('jobs', [JobController::class, 'store' ]);
    Route::delete('jobs/{job}', [JobController::class, 'destroy']);

    Route::post('company/logout', [CompanyAuthController::class, 'logout']);
});

Route::middleware('api_guest')->group(function () {
    Route::prefix('company')->controller(CompanyAuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
});
