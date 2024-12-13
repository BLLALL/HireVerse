<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'business_email',
        'location',
        'website_url',
        'ceo',
        'description',
        'insights',
        'industry',
        'employee_no',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->BelongsToMany(Plan::class, 'subscriptions')->withPivot('type', 'price');
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(Applicant::class, 'reviews')->withPivot(
            'rating',
            'body',
            'employment_status',
            'is_current_employee'
        );
    }
}
