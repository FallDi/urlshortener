<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller;

use App\Common\Clock;
use App\Controller\V1\Request\CreateUrlRequest;
use App\Controller\V1\UrlController;
use App\Service\Url\UrlShortener;
use App\Tests\Application\BaseWebTestCase;
use DateInterval;
use DateTimeImmutable;
use Helmich\JsonAssert\JsonAssertions;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

#[CoversMethod(UrlController::class, 'create')]
#[CoversMethod(UrlController::class, 'view')]
class UserUrlControllerTest extends BaseWebTestCase
{
    use JsonAssertions;

    public static function providerCreateWithWrongParameters(): array
    {
        return [
            'empty params' => [
                'params' => [],
                'expected' => [
                    'message' => 'Invalid request',
                    'violations' => [
                        ['path' => 'url', 'message' => 'This value should be of type string.'],
                    ],
                ],
            ],
            'empty string' => [
                'params' => [
                    'url' => '',
                    'expiresAt' => '',
                ],
                'expected' => [
                    'message' => 'Invalid request',
                    'violations' => [
                        ['path' => 'url', 'message' => 'This value should not be blank.'],
                    ],
                ],
            ],
            'invalid params format' => [
                'params' => [
                    'url' => 'http://',
                    'expiresAt' => '2021-01-02',
                ],
                'expected' => [
                    'message' => 'Invalid request',
                    'violations' => [
                        ['path' => 'url', 'message' => 'This value is not a valid URL.'],
                        ['path' => 'expiresAt', 'message' => 'This value is not a valid datetime.'],
                    ],
                ],
            ],
            'past expiredAt' => [
                'params' => [
                    'url' => 'http://localhost.local/',
                    'expiresAt' => new DateTimeImmutable('now')->format(CreateUrlRequest::EXPIRES_AT_FORMAT),
                ],
                'expected' => [
                    'message' => 'expiresAt must be greater than current time',
                ],
            ],
        ];
    }

    #[DataProvider('providerCreateWithWrongParameters')]
    public function testCreateWithWrongParameters(array $params, array $expected): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/v1/urls', $params);
        $responseContent = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertJson($responseContent);
        self::assertJsonStringEqualsJsonString(json_encode($expected), $responseContent);
    }

    public function testCreateOk(): void
    {
        $client = static::createClient();
        $payload = [
            'url' => 'http://localhost.local/test1/test2',
            'expiresAt' => new DateTimeImmutable('now + 1 day')->format(CreateUrlRequest::EXPIRES_AT_FORMAT),
        ];
        $client->jsonRequest('POST', '/api/v1/urls', $payload);
        $responseContent = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertJson($responseContent);
        self::assertJsonDocumentMatchesSchema($responseContent, [
            'type'       => 'object',
            'required'   => ['shortUrl'],
            'properties' => [
                'shortUrl' => ['type' => 'string', 'minLength' => 10],
            ],
        ]);
    }

    public function testViewInvalidUrl(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/v1/urls/!');
        $responseContent = $client->getResponse()->getContent();

        $expected = [
                'message' => 'Invalid short URL',
        ];
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        self::assertJson($responseContent);
        self::assertJsonStringEqualsJsonString(json_encode($expected), $responseContent);
    }

    public function testViewExpiredUrl(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        /** @var UrlShortener $urlShortener */
        $urlShortener = $container->get(UrlShortener::class);
        /** @var Clock $clock */
        $clock = $container->get(Clock::class);
        $url = 'http://localhost.local/some-test';
        $shortUrl = $urlShortener->createShortUrl($url, $clock->now()->sub(new DateInterval('PT1H')));

        $client->jsonRequest('GET', $shortUrl);
        $responseContent = $client->getResponse()->getContent();

        $expected = [
            'message' => 'Short URL is expired and not available anymore',
        ];
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        self::assertJson($responseContent);
        self::assertJsonStringEqualsJsonString(json_encode($expected), $responseContent);
    }

    public function testViewOk(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        /** @var UrlShortener $urlShortener */
        $urlShortener = $container->get(UrlShortener::class);
        $url = 'http://localhost.local/some-test';
        $shortUrl = $urlShortener->createShortUrl($url, null);

        $client->jsonRequest('GET', $shortUrl);
        $responseContent = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertEmpty($responseContent);
        self::assertResponseHeaderSame('Location', $url);
    }
}
