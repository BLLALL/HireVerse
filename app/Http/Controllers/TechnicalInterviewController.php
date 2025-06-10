<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Interview;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TechnicalInterviewController extends Controller
{
    use ApiResponses;

    public function index(Interview $interview) {
        
        if(Carbon::now()->greaterThan($interview->deadline)) {
            return $this->error('Interview deadline has passed', 400);
        }


        $questions = Question::where('interview_id', $interview->id)->get(['id', 'question', 'difficulty', 'expected_keywords', 'assessment_criteria', 'interview_id']);

        return response()->json($questions);
    }
    
    public function update(Request $request, Question $question)
    {
       $validator = Validator::make($request->all(), [
            'applicant_answer' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

            $file = $request->file('applicant_answer')->store('applicant_answers', 'public');
            
            $question->update([
                'applicant_answer' => $file,
            ]);
            
            return response()->json([
                'message' => 'Applicant answer uploaded successfully',
            ]);
    }
}
