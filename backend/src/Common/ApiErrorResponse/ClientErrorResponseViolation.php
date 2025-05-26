<?php

declare(strict_types=1);

namespace App\Common\ApiErrorResponse;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClientErrorResponseViolation',
    required: ['path', 'message'],
    properties: [
        new OA\Property('path', type: 'string'),
        new OA\Property('message', type: 'string'),
    ],
)]
readonly class ClientErrorResponseViolation
{
    public function __construct(public string $path, public string $message)
    {
    }
}
