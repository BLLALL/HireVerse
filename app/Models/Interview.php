<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $fillable = [
        'question',
        'difficulty',
        'ideal_answer',
        'applicant_answer',
        'score',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    public function question(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
