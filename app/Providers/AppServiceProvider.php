<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Filament\Events\Auth\Registered;
use Illuminate\Auth\Events\Login;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        $this->app->singleton(InvoiceService::class);
//        $this->app->singleton(RateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'engineer' => \App\Models\Engineer::class,
//            'employee' => \App\Models\Employee::class,
//            'subcontractor' => \App\Models\Subcontractor::class,
            'company' => \App\Models\Company::class,
            'client' => \App\Models\Client::class,
            'project' => \App\Models\Project::class,
            'user' => \App\Models\User::class,
        ]);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

    }
}
