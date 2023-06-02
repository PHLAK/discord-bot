<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register() {}

    /** Bootstrap any application services. */
    public function boot(): void
    {
        RateLimiter::for('discord-webhooks', function () {
            return Limit::perMinute(60);
        });
    }
}
