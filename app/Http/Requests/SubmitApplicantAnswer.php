<?php

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SubmitApplicantAnswer extends FormRequest
{
    public function authorize(): bool
    {
        $question = request()->route('question');
        $interviewId = $question->interview_id;

        return Application::find($interviewId)->applicant_id == Auth::id()
            && $question->applicant_answer == null;
            // && Interview::find($interviewId)->deadline > now();
    }

    public function rules(): array
    {
        return [
            'applicant_answer' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400',
        ];
    }
}
