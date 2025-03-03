<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'summary',
        'salary',
        'work_hours',
        'work_location',
        'experience_level',
        'responsibilities',
        'requirements',
        'work_location',
        'is_available',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    public function applicants()
    {
        $this->belongsToMany(Applicant::class, 'applications', 'applicant_id', 'job_id')
            ->withPivot('cv', 'cv_score', 'cv_accepted');
    }

    public function skills(): MorphMany
    {
        return $this->morphMany(Skill::class, 'skillable');
    }

    public function getSkillsAttribute() 
    {
        return $this->skills()->pluck('title');
    }
}
