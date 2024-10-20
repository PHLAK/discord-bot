<?php

namespace App\Listeners\Traits;

use App\Support\Time\Seconds;

trait RetryWithBackoff
{
    public int $tries = 10;
    public int $maxExceptions = 5;

    /** @return array<int> */
    public function backoff(): array
    {
        return [
            Seconds::ONE_MINUTE,
            Seconds::TWO_MINUTES,
            Seconds::THREE_MINUTES,
            Seconds::FOUR_MINUTES,
            Seconds::FIVE_MINUTES,
        ];
    }
}
