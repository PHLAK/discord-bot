<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use JsonException;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException()
                || $entry->isRequest()
                || $entry->isClientRequest()
                || $entry->isScheduledTask()
                || $entry->hasMonitoredTag()
                || $entry->type === EntryType::EVENT
                || $entry->type === EntryType::JOB
                || $entry->type === EntryType::LOG;
        });

        Telescope::tag(function (IncomingEntry $entry) {
            if ($entry->type !== EntryType::REQUEST) {
                return [];
            }

            if (! array_key_exists('payload', $entry->content)) {
                return [];
            }

            try {
                $payload = json_decode($entry->content['payload'], flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return [];
            }

            return [sprintf('event:%s', $payload->event)];
        });
    }

    /** Prevent sensitive request details from being logged by Telescope. */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'Chris@ChrisKankiewicz.com',
            ]);
        });
    }
}
