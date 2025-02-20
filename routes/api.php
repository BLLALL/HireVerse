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
Route::get("/email/verify/{id}", [
    VerificationController::class,
    "verify",
])->name("verification.verify");
Route::get("/email/resend", [AuthController::class, "resend"])->name(
    "verification.verify"
);

Route::get("/jobs", [JobController::class, "index"]);
Route::get("/email/verify/{id}", function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect("/home");
    event(new Registered($user));
})->name("verification.verify");

Route::get("email/resend", [VerificationController::class, "resend"])->name(
    "verification.resend"
);

// Route::get("/jobs", [JobController::class, "index"]);
Route::apiResource("/jobs", JobController::class)->only(["index", "show"]);
Route::apiResource("/companies", CompanyController::class)->only([
    "index",
    "show",
]);
Route::post("/jobs/apply", [JobController::class, "apply"]);

Route::get("/users", function () {
    return Applicant::all();
});
