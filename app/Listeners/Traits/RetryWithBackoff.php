<?php

namespace App\Listeners\Traits;

use App\Support\Time\Seconds;

trait RetryWithBackoff
{
    public int $tries = 10;
    public int $maxExceptions = 5;

    public function backoff(): array
    {
        return [
            Seconds::ONE_MINUTE,
            Seconds::THREE_MINUTES,
            Seconds::FIVE_MINUTES,
            Seconds::FIFTEEN_MINUTES,
        ];
    }
}
