<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Enums\WorkingHours as EnumsWorkingHours;
use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class WorkingHours
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('working_hours') && in_array(request()->work_hours, EnumsWorkingHours::values())) {
            $query->where('working_hours', request()->working_hours);
        }

        return $next($query);
    }
}
