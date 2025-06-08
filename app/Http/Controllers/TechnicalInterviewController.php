<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Question;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TechnicalInterviewController extends Controller
{
    use ApiResponses;

    public function index(Interview $interview) {
        $questions = Question::where('interview_id', $interview->id)->get(['id', 'question', 'difficulty', 'expected_keywords', 'assessment_criteria', 'interview_id']);

        return response()->json($questions);
    }
    
    public function update(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'applicant_answer' => 'required|file|mimes:mp3,ogg,wav/max:50000', // 50MB max size
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        if ($request->hasFile(('applicant_answer')))
        {
            $file = $request->file('applicant_answer')->store('applicant_answers', 'public');

            Question::update([
                'applicant_answer' => $file,
            ]);
            
            return response()->json([
                'message' => 'Applicant answer uploaded successfully',
            ]);

        }
    }
}
