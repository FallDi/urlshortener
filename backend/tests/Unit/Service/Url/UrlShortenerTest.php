<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Url;

use App\Common\RouteNames;
use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Service\Url\BaseConverter;
use App\Service\Url\UrlShortener;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversMethod(UrlShortener::class, 'createShortUrl')]
#[CoversMethod(UrlShortener::class, 'convertShortUrlIdToUrl')]
class UrlShortenerTest extends TestCase
{
    public function testCreateShortUrl(): void
    {
        $url = 'http://localhost/test/123';
        $expiredAt = new DateTimeImmutable('2032-01-02 00:02:03');

        $urlRepository = $this->getMockBuilder(UrlRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlId = 2135;
        $urlRepositoryReturnedUrl = $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlRepositoryReturnedUrl->expects($this->once())
            ->method('getId')
            ->willReturn($urlId);
        $urlRepository->expects($this->once())
            ->method('create')
            ->with(new Url($url, $expiredAt))
            ->willReturn($urlRepositoryReturnedUrl);

        $baseConverter = $this->getMockBuilder(BaseConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $shortUrlId = 'ab1';
        $baseConverter->expects($this->once())
            ->method('base10ToBaseShortUrl')
            ->with($urlId)
            ->willReturn($shortUrlId);

        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expectedShortUrl = "http://localhost/bla-bla/{$shortUrlId}";
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with(RouteNames::PUBLIC_SHORT_URL_VIEW, ['id' => $shortUrlId], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($expectedShortUrl);

        $urlShortener = new UrlShortener($baseConverter, $urlGenerator, $urlRepository);
        $this->assertSame($expectedShortUrl, $urlShortener->createShortUrl($url, $expiredAt));
    }

    public function testConvertShortUrlIdToUrl(): void
    {
        $shortUrlId = 'abc';
        $urlId = 12345;

        $baseConverter = $this->getMockBuilder(BaseConverter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $baseConverter->expects($this->once())
            ->method('baseShortUrlToBase10')
            ->with($this->equalTo($shortUrlId))
            ->willReturn($urlId);

        $url = new Url($shortUrlId, null);
        $urlRepository = $this->getMockBuilder(UrlRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlRepository->expects($this->once())
            ->method('findWithCache')
            ->with($urlId)
            ->willReturn($url);

        $urlShortener = new UrlShortener(
            $baseConverter,
            $this->getMockBuilder(UrlGeneratorInterface::class)->getMock(),
            $urlRepository,
        );

        $this->assertSame($url, $urlShortener->convertShortUrlIdToUrl($shortUrlId));
    }
}
