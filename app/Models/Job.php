<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\JobPhase;
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
        'job_location',
        'experience_level',
        'responsibilities',
        'requirements',
        'work_location',
        'max_applicants',
        'is_available',
        'available_to',
        'phase',
        'company_id',
    ];

    protected $casts = [
        'phase' => JobPhase::class,
    ];

    public function scopeAvailable()
    {
        return Job::whereIsAvailable(true);
    }

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

    public function setSkillsAttribute($skills)
    {
        if (empty($skills)) {
            return;
        }

        $uniqueSkills = array_unique($skills);
        $skills = array_map(function ($skill) {
            return [
                'title' => $skill,
                'skillable_type' => self::class,
                'skillable_id' => $this->id,
            ];
        }, $uniqueSkills);

        $this->skills()->delete();
        Skill::insert($skills);
    }

    public function skills(): MorphMany
    {
        return $this->morphMany(Skill::class, 'skillable');
    }

    public function getSkillsTitlesAttribute()
    {
        return $this->skills()->pluck('title');
    }

    public function scopePublishedLastMonth($query)
    {
        return $query->whereMonth('created_at', now()->subMonth()->month);
    }

    public function scopeWithAcceptedApplicants($query)
    {
        return $query->withCount(['applicants' => function ($q) {
            $q->where('applications.status', ApplicationStatus::Accepted);
        }]);
    }

    public function scopePublishedThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopeAcceptedThisMonth($query)
    {
        return $query->whereHas('applicants', function ($q) {
            $q->where('applications.status', ApplicationStatus::Accepted)
                ->whereMonth('applications.created_at', now()->month);
        });
    }

    public function scopeAcceptedLastMonth($query)
    {
        return $query->whereHas('applicants', function ($q) {
            $q->where('applications.status', ApplicationStatus::Accepted)
                ->whereMonth('applications.created_at', now()->subMonth()->month);
        });
    }

    public function scopeApplicationsThisMonth($query)
    {
        return $query->whereHas('applicants', function ($q) {
            $q->whereMonth('applications.created_at', now()->month);
        });
    }

    public function scopeApplicationsLastMonth($query)
    {
        return $query->whereHas('applicants', function ($q) {
            $q->whereMonth('applications.created_at', now()->subMonth()->month);
        });
    }
}
