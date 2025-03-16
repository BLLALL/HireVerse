<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Enums\WorkLocation;
use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Location
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('location')) {
            $locations = explode(',', request()->get('location'));
            $validLocations = array_intersect($locations, WorkLocation::values());
            if (! empty($validLocations)) {
                $query->whereIn('work_location', $validLocations);
            }
        }

        return $next($query);
    }
}
