<?php

declare(strict_types=1);

namespace App\Service\UserMeta;

readonly class UserMetaDataRaw
{
    public function __construct(public ?string $ip, public ?string $userAgent)
    {
    }
}
