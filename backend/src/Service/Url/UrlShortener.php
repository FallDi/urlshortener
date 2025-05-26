<?php

declare(strict_types=1);

namespace App\Service\Url;

use App\Entity\Url;
use App\Repository\UrlRepository;
use DateTimeImmutable;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class UrlShortener
{
    public function __construct(
        private BaseConverter $baseConverter,
        private UrlGeneratorInterface $urlGenerator,
        private UrlRepository $urlRepository,
    ) {

    }

    public function createShortUrl(string $url, ?DateTimeImmutable $expiredAt): string
    {
        $urlObject = new Url($url, $expiredAt);
        $urlObject = $this->urlRepository->create($urlObject);
        $shortUrlId = $this->baseConverter->base10ToBaseShortUrl($urlObject->getId());

        return $this->urlGenerator->generate(
            'short-url-view',
            ['id' => $shortUrlId],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    public function convertShortUrlIdToUrl(string $shortUrlId): ?Url
    {
        $urlId = $this->baseConverter->baseShortUrlToBase10($shortUrlId);

        return $this->urlRepository->findWithCache($urlId);
    }
}
