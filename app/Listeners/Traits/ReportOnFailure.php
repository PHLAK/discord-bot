<?php

namespace App\Listeners\Traits;

use Throwable;

trait ReportOnFailure
{
    public function failed(mixed $event, Throwable $exception): void
    {
        if (method_exists($this, 'onFailure')) {
            $this->onFailure($exception);
        }

        report($exception);
    }
}
