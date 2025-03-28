<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Search
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('search')) {
            $search_term = strtolower(request()->search);
            $query->whereRaw('LOWER(title) like ?', ["%{$search_term}%"])
                ->orWhereHas('company', function ($q) use ($search_term) {
                    $q->whereRaw('LOWER(name) like ?', ["%{$search_term}%"]);
                })
                ->orWhereHas('skills', function ($q) use ($search_term) {
                    $q->whereRaw('LOWER(title) like ?', ["%{$search_term}%"]);
                });
        }

        return $next($query);
    }
}
