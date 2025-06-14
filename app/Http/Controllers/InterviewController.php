<?php

namespace App\Http\Controllers;

use App\Events\ApplicantConductedInterview;
use App\Http\Requests\SubmitApplicantAnswer;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Question;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    use ApiResponses;

    public function index(Interview $interview)
    {
        $questions = Question::where('interview_id', $interview->id)->orderBy('id')->get(['id', 'question', 'difficulty', 'interview_id']);

        return response()->json($questions);
    }

    public function submitAnswer(SubmitApplicantAnswer $request, Question $question)
    {
        $file = $request->file('applicant_answer');

        $path = "interviews/{$question->interview_id}/answers";
        $name = "q-{$question->id}.{$file->getClientOriginalExtension()}";

        $attributes['applicant_answer'] = $file->storeAs($path, $name);

        $question->update($attributes);

        $applicantCompletedInterview = ! Question::whereInterviewId($question->interview_id)->whereNull('applicant_answer')->exists();

        if ($applicantCompletedInterview) {
            Application::find($question->interview_id)->update(['status' => ApplicationStatus::Interviewed]);
            ApplicantConductedInterview::dispatch($question->interview()->first());
        }


        return response()->json([
            'message' => 'Applicant answer uploaded successfully',
        ]);
    }

    public function storeResults(Request $request, Interview $interview)
    {
        $results = $request->post('results');
        $technical = $results['technicalSkills'];
        $soft = $results['softSkills'];

        $questions = $interview->questions()->orderBy('id')->get();

        $sum = 0;

        for ($i=0; $i < $questions->count(); $i++) {
            $score = (float) $technical[$i]['scores']['overall'];
            $questions[$i]->update(['applicant_score' => $score]);
            $sum += $score;
        }
        
        $interview->update([
            'technical_skills_score' => round((float) $sum / $questions->count(), 2),
            'soft_skills_score' => round((float) $soft['overall'], 2),
        ]);
    }

}
