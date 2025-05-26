<?php

declare(strict_types=1);

namespace App\UseCase\CreateShortUrl;

use App\Common\Clock;
use App\Repository\UserMetaDataRepository;
use App\Service\Url\UrlShortener;
use App\Service\UserMeta\UserMetaDataParser;

/**
 * Create short URL from long URL
 */
readonly class CreateShortUrlUseCase
{
    public function __construct(
        private Clock $clock,
        private UrlShortener $urlShortener,
        private UserMetaDataParser $userMetaDataParser,
        private UserMetaDataRepository $userMetaDataSaver,
    ) {
    }

    public function handle(CreateShortUrlCommand $command): string
    {
        if ($command->expiresAt) {
            $now = $this->clock->now();
            if ($command->expiresAt <= $now) {
                throw new PastExpiresAtException('expiresAt must be greater than current time');
            }
        }

        $shortUrl = $this->urlShortener->createShortUrl($command->url, $command->expiresAt);
        // Possible improvement: handle via background worker to handle http response faster
        $userMetaDataParsed = $this->userMetaDataParser->parse($command->clientMetaData);
        $this->userMetaDataSaver->save($userMetaDataParsed);

        return $shortUrl;
    }
}
