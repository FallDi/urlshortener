<?php

declare(strict_types=1);

namespace App\Common;

use DateTimeImmutable;
use Override;
use Psr\Clock\ClockInterface;

class Clock implements ClockInterface
{
    #[Override]
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
