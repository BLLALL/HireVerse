<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'deadline',
        'technical_skills_score',
        'soft_skills_score',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
