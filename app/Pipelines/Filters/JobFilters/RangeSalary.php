<?php

namespace App\Pipelines\Filters\JobFilters;

use App\Traits\Filtrable;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class RangeSalary
{
    use Filtrable;

    public function handle(Builder $query, Closure $next)
    {
        $minSalary = request()->min_salary;
        $maxSalary = request()->max_salary;

        if (isset($minSalary) && isset($maxSalary)) {
            $query->whereBetween('salary', [$minSalary, $maxSalary]);
        } elseif (isset($minSalary)) {
            $query->where('salary', '>=', $minSalary);
        } elseif (isset($maxSalary)) {
            $query->where('salary', '<=', $maxSalary);
        }

        return $next($query);
    }
}
