<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("/register", [AuthController::class, "register"]);
Route::get("/jobs", [JobController::class, "index"]);
Route::get("/email/verify/{id}/{hash}", function (
    EmailVerificationRequest $request
) {
    $request->fulfill();

    return redirect("/home");
    event(new Registered($user));
})
    ->middleware(["auth", "signed"])
    ->name("verification.verify");
