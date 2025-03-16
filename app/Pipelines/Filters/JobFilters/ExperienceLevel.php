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
        if (request()->has('experience_level')) {
            $experienceLevels = explode(',', request()->get('experience_level'));

            $validLevels = array_intersect($experienceLevels, EnumsExperienceLevel::values());

            if (! empty($validLevels)) {
                $query->whereIn('experience_level', $validLevels);
            }
        }

        return $next($query);
    }
}
