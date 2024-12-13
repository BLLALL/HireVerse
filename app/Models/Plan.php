<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    
    protected $fillable = [
        'type',
        'price',
    ];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'subscriptions')
            ->withPivot('type', 'price');
    }


}
