<?php

declare(strict_types=1);

namespace App\Tests\Application\Controller\Internal;

use App\Controller\V1\Internal\HealthCheckController;
use App\Tests\Application\BaseWebTestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Response;

#[CoversMethod(HealthCheckController::class, 'startupProbe')]
class HealthCheckControllerTest extends BaseWebTestCase
{
    public function testViewInvalidUrl(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/internal/v1/probes/startup');
        $responseContent = $client->getResponse()->getContent();
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertSame('', $responseContent);
    }
}
