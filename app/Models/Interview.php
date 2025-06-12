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
        'deadline',
        'application_id',
    ];

    protected $casts = [
        'deadline' => 'datetime',
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
