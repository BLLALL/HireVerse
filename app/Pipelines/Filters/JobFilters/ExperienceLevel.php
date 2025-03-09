<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Enums\ExperienceLevel as EnumsExperienceLevel;
use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ExperienceLevel
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('experience_level') &&
            in_array(request()->experience_level, EnumsExperienceLevel::values())) {
            $query->where('experience_level', request()->experience_level);

        }

        return $next($query);
    }
}
