<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Interview extends Model
{
    protected $fillable = [
        'question',
        'difficulty',
        'ideal_answer',
        'applicant_answer',
        'score',
        'application_id'
    ];

    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
