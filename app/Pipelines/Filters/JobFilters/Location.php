<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\WorkLocation;
class Location
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('location')) {
            $locations = explode(',', request()->get('location'));
            $validLocations = array_filter($locations, fn($location) => in_array($location, WorkLocation::values(), true));
            if(!empty($validLocations)){
                $query->whereIn('work_location', $validLocations);
            }
        }

        return $next($query);
    }
}
