<?php

declare(strict_types=1);

namespace App\UseCase\CreateShortUrl;

use App\Service\UserMeta\UserMetaDataRaw;
use DateTimeImmutable;

readonly class CreateShortUrlCommand
{
    public function __construct(
        public string $url,
        public ?DateTimeImmutable $expiresAt,
        public UserMetaDataRaw $clientMetaData,
    ) {

    }
}
