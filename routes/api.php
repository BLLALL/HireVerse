<?php

<<<<<<< HEAD
use App\Http\Controllers\AuthController;
=======
use App\Http\Controllers\CompanyController;
>>>>>>> 06adc9c3770f77f17aede56de044c83d0dc57759
use App\Http\Controllers\JobController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

<<<<<<< HEAD
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
=======
// Route::get("/jobs", [JobController::class, "index"]);
Route::apiResource('/jobs', JobController::class)->only(['index', 'show']);
Route::apiResource('/companies', CompanyController::class)->only(['index', 'show']);
Route::post('/jobs/apply', [JobController::class, 'apply']);
>>>>>>> 06adc9c3770f77f17aede56de044c83d0dc57759
