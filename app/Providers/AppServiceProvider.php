<?php

namespace App\Providers;

use App\Models\Applicant;
use App\Models\Company;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('applicant', fn ($user) => $user instanceof Applicant);
        Gate::define('company', fn ($user) => $user instanceof Company);
    }
}
