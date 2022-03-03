<?php

namespace App\Providers;

use App\Listeners\LibraryNew;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    /** Array mapping classes to their required disk names. */
    private const CLASS_TO_DISK_MAP = [
        LibraryNew::class => 'public',
    ];

    /** Register services. */
    public function register()
    {
        Collection::make(self::CLASS_TO_DISK_MAP)->each(function (string $class, string $disk): void {
            $this->app->when($class)->needs(Filesystem::class)->give(
                fn (Application $app): Filesystem => $app->runningUnitTests() ? Storage::fake($disk) : Storage::disk($disk)
            );
        });
    }
}
