<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchApplicants
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('search')) {
            $search = strtolower(request()->search);
            $query->whereRaw("LOWER(CONCAT(first_name, CONCAT(' ', last_name))) like ?", ["%$search%"]);
        }

        return $next($query);
    }
}
