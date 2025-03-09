<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class search
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('search')) {
            $query->where('title', 'like', '%'.request()->search.'%')
                ->orWhereRelation('company', 'name', 'like', '%'.request()->search.'%')
                ->orWhereRelation('skills', 'title', 'like', '%'.request()->search.'%');
        }

        return $next($query);
    }
}
