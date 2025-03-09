<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Location
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('location')) {
            $query->where('work_location', request()->location);
        }

        return $next($query);
    }
}
