<?php

declare(strict_types=1);

namespace App\Service\UserMeta;

readonly class UserMetaDataParsed
{
    public function __construct(public string $os, public string $browser)
    {
    }
}
