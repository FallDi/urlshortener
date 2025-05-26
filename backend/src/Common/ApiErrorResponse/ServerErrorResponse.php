<?php

declare(strict_types=1);

namespace App\Common\ApiErrorResponse;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'serverError',
    description: 'Internal server error response',
    content: new OA\JsonContent(
        required: ['message'],
        properties: [
            new OA\Property('message', type: 'string'),
        ],
    ),
)]
class ServerErrorResponse extends BaseErrorResponse
{
}
