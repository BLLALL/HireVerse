<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Models\Applicant;

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::get("/email/verify/{id}", [VerificationController::class, "verify"])->name("verification.verify");

Route::middleware(["throttle:6,1", "abilities:email-verification"])->group(function () {
    Route::post("email/resend", [VerificationController::class, "resend"])->name("verification.resend");
});

Route::middleware("auth:sanctum", "ability:*")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
   # Route::apiResource("/jobs", JobController::class)->only(["index", "show"]);
   # Route::apiResource("/companies", CompanyController::class)->only(["index", "show"]);
    Route::post("/jobs/apply", [JobController::class, "apply"]);

    Route::get("/users", function () {
        return Applicant::all();
    });
});

Route::apiResource("/jobs", JobController::class)->only(["index", "show"]);
Route::apiResource("/companies", CompanyController::class)->only(["index", "show"]);

Route::prefix('oauth')->controller(OAuthController::class)->group(function () {
    Route::get('{provider}/redirect', 'redirect');
    Route::get('{provider}/callback', 'callback');
    Route::post('{provider}/complete', 'complete');
});

Route::get('test', fn() => 'HireVerse - HierServe');
