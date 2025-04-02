<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Applicant extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'birthdate',
        'cv',
        'job_title',
        'github_url',
        'linkedin_url',
        'college',
        'department',
        'provider',
        'provider_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'applications', 'applicant_id', 'job_id')
            ->orderByPivot('created_at', 'desc')
            ->withPivot('id', 'status', 'cv', 'cv_score')
            ->withTimestamps()
            ->as('application');
    }

    public function skills(): MorphMany
    {
        return $this->morphMany(Skill::class, 'skillable');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'reviews')->withPivot(
            'rating',
            'body',
            'employment_status',
            'is_current_employee'
        );
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

    public function getSkillsAttribute()
    {
        return $this->skills()->pluck('title');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification);
    }
}
