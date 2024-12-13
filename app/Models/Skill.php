<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'title',
        'skillable_id',
        'skillable_type',
    ];

    public function skillable() 
    {
        return $this->morphTo();
    }
}
