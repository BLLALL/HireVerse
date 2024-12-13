<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'cv',
        'cv_score',
        'cv_accepted',
        'applicant_id',
        'job_id',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
