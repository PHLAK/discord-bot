<?php

namespace App\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    /** Register services. */
    public function register()
    {
        /** @var array $diskMap */
        $diskMap = config('filesystems.disk_map', []);

        Collection::make($diskMap)->each(function (string $disk, string $class): void {
            $this->app->when($class)->needs(Filesystem::class)->give(
                fn (Application $app): Filesystem => $app->runningUnitTests() ? Storage::fake($disk) : Storage::disk($disk)
            );
        });
    }
}
