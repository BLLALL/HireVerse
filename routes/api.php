<?php

use App\AIServices\QuestionsGeneration;
use App\AIServices\Recommendation;
use App\Models\Job;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use App\Events\ApplicantApplied;
use App\Events\ApplicantConductedInterview;
use App\Events\InterviewPhaseStarted;
use App\Events\ScheduleInterview;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\VerificationController;
use App\Models\Interview;
use App\Models\Question;

require_once __DIR__ . '/api_applicant.php';
require_once __DIR__ . '/api_company.php';



Route::post('/github-webhook', function (\Illuminate\Http\Request $request) {
    $secret = env('GITHUB_SECRET');
    $signature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

    if (!hash_equals($signature, $request->header('X-Hub-Signature-256'))) {
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    $process = new Process(['git', 'pull']);
    $process->run();

    if (!$process->isSuccessful()) {
        return response()->json(['message' => 'Git pull failed!'], 500);
    }

    return response()->json(['message' => 'Git pull executed successfully!']);
});



Route::controller(VerificationController::class)->group(function () {
    Route::get('{type}/email/verify/{id}', 'verify')->name('verification.verify');
    Route::post('email/resend', 'resend')->middleware(['auth:sanctum', 'throttle:6,1', 'abilities:email-verification'])->name('verification.resend');
});

Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);
Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
Route::apiResource('applicants', ApplicantController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'ability:*', 'verified'])->group(function () {
    Route::get('auth/user', CurrentUserController::class);
});

Route::middleware('signed')->post('interviews/{interview}/analysis-callback', [InterviewController::class, 'storeResults'])
    ->name('interviews.analysis.callback');


Route::get('storage/{filePath}', function ($filePath) {
    if (! Storage::exists($filePath)) {
        return response()->json(['message' => 'File not found.'], 404);
    }
    return response()->file(public_path('storage/' . $filePath));
})->where('filePath', '.*');


Route::get('test', function () {

    // Storage::put('interviews/56/questions.json', Storage::disk('public')->get('interviews/82/questions.json'));

    // $questions = json_decode(, true);


    // $interview = Application::whereApplicantId(1)->whereJobId(1)->first()->interview()->create();

    // $questions = collect($questions)->map(function ($q) use ($interview) {
    //     return [
    //         'question' => $q['question'],
    //         'difficulty' => $q['difficulty'],
    //         'interview_id' => $interview->id
    //     ];
    // })->toArray();

    // Question::insert($questions);

    // ApplicantConductedInterview::dispatch(Interview::find(56));

    // $questions = Question::where('interview_id', 1)->get();
    // InterviewPhaseStarted::dispatch($job);
    // return response()->json($questions);


    //Application::whereJobId($job->id)->update([
    //    'status' => ApplicationStatus::Pending,
    //   'cv_score' => null
    //]);

    //ApplicantApplied::dispatch($job);


    return 'HireVerse - HierServe';
});
