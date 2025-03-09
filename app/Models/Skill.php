<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use SoftDeletes;

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
