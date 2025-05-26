<?php

declare(strict_types=1);

namespace App\UseCase\ViewShortUrl;

use App\Service\Url\BaseConverter;
use App\Service\Url\UrlShortener;
use Psr\Clock\ClockInterface;

/**
 * View long URL by short URL
 */
readonly class ViewShortUrlUseCase
{
    public function __construct(private UrlShortener $urlShortener, private ClockInterface $clock)
    {
    }

    public function handle(ViewShortUrlCommand $command): string
    {
        if (!preg_match('/[' . preg_quote(BaseConverter::SHORT_URL_ALPHABET) . ']+/', $command->shortUrlId)) {
            throw new ShortUrlNotFoundException('Invalid short URL');
        }

        $url = $this->urlShortener->convertShortUrlIdToUrl($command->shortUrlId);

        if (!$url) {
            throw new ShortUrlNotFoundException('Short url not found');
        }

        if ($url->getExpiredAt() && $this->clock->now() >= $url->getExpiredAt()) {
            throw new ShortUrlNotFoundException('Short URL is expired and not available anymore');
        }

        return $url->getUrl();
    }
}
