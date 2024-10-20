<?php

namespace App\Providers;

use App\Listeners\LibraryNew;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->when(LibraryNew::class)->needs(Filesystem::class)->give(
            fn (Application $app): Filesystem => Storage::disk('public')
        );
    }

    public function boot(): void
    {
        RateLimiter::for('discord-webhooks', function () {
            return Limit::perMinute(60);
        });
    }
}
