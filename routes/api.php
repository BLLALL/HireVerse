<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Models\Applicant;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::get("/email/verify/{id}", [
    VerificationController::class,
    "verify",
])->name("verification.verify");

Route::middleware("throttle:6,1")->group(function () {
    Route::get("email/resend", [VerificationController::class, "resend"])->name(
        "verification.resend"
    );
});

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::apiResource("/jobs", JobController::class)->only(["index", "show"]);
    Route::apiResource("/companies", CompanyController::class)->only([
        "index",
        "show",
    ]);
    Route::post("/jobs/apply", [JobController::class, "apply"]);

    Route::get("/users", function () {
        return Applicant::all();
    });
});
