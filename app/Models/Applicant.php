<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "email",
        "password",
        "birth_date",
        "cv",
        "job_title",
        "github_url",
        "linkedin_url",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    public function jobs()
    {
        $this->belongsToMany(
            Job::class,
            "applications",
            "job_id",
            "applicant_id"
        )->withPivot("cv", "cv_score", "cv_accepted");
    }

    public function skills(): MorphMany
    {
        return $this->morphMany(Skill::class, "skillable");
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, "reviews")->withPivot(
            "rating",
            "body",
            "employment_status",
            "is_current_employee"
        );
    }
}
