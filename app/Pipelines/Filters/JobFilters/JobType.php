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
        if (request()->has('type') && in_array(request()->type, EnumsJobType::values())) {
            $query->where('type', request()->type);
        }

        return $next($query);
    }
}
