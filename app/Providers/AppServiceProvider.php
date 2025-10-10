<?php

namespace App\Providers;

use App\Services\InvoiceService;
use App\Services\RateService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(RateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
        'client'        => \App\Models\Client::class,
        'subcontractor' => \App\Models\Subcontractor::class,
        'employee'      => \App\Models\Employee::class,
        'worker'        => \App\Models\Worker::class,
        'user'          => \App\Models\User::class,
    ]);
    }
}
