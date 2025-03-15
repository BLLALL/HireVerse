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
        if (request()->has('work_hours') && in_array(request()->work_hours, EnumsWorkingHours::values())) {
            $query->where('work_hours', request()->work_hours);
        }

        return $next($query);
    }
}
