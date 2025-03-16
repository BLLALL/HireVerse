<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Enums\JobType as EnumsJobType;
use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class JobType
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('type')) {
            $types = explode(',', request()->get('type'));

            $validTypes = array_intersect($types, EnumsJobType::values());

            if (! empty($validTypes)) {
                $query->whereIn('type', $validTypes);
            }
        }

        return $next($query);
    }
}
