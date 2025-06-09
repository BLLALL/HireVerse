<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question',
        'applicant_answer',
        'difficulty',
        'interview_id',
    ];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }
}
