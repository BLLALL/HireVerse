<?php

namespace App\Models;

use App\Traits\Filtrable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use Filtrable, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'summary',
        'salary',
        'currency',
        'work_hours',
        'work_location',
        'experience_level',
        'responsibilities',
        'requirements',
        'work_location',
        'max_applicants',
        'is_available',
        'available_to',
        'company_id',
    ];

    protected $casts = [
        'available_to' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(Applicant::class, 'applications', 'job_id', 'applicant_id')
            ->orderByPivot('created_at', 'desc')
            ->withPivot('status', 'cv', 'cv_score')
            ->withTimestamps()
            ->as('application');
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
