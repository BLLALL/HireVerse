<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class technicalInterviewController extends Controller
{
    public function store(Request $request)
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
