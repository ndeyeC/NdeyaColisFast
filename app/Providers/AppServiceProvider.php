<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       $this->app->singleton(Firebase::class, function ($app) {
        return (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createMessaging();
    });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
