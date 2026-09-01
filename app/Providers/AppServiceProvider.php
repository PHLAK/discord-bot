<?php

namespace App\Providers;

use App\Listeners\LibraryNew;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\PocketID;

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
        Date::use(CarbonImmutable::class);

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('pocketid', PocketID\Provider::class);
        });

        RateLimiter::for('discord-webhooks', function () {
            return Limit::perMinute(60);
        });

        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, ['Chris@Kankiewicz.com']);
        });
    }
}
