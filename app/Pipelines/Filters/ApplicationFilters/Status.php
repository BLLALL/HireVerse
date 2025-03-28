<?php

namespace App\Pipelines\Filters\ApplicationFilters;

use App\Enums\ApplicationStatus;
use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Status
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('status')) {
            $statuses = explode(',', request()->get('status'));
            $statuses = array_intersect($statuses, ApplicationStatus::values());
            if (! empty($statuses)) {
                $query->whereIn('applications.status', $statuses);
            }
        }

        return $next($query);
    }
}
