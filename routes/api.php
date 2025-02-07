<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get("/jobs", [JobController::class, "index"]);
Route::apiResource('/jobs', JobController::class)->only(['index', 'show']);
Route::apiResource('/companies', CompanyController::class)->only(['index', 'show']);
Route::post('/jobs/apply', [JobController::class, 'apply']);
