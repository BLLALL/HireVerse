<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv',
        'cv_score',
        'cv_accepted',
        'applicant_id',
        'job_id',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    // public function applicant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo(Applicant::class);
    // }
    
}
