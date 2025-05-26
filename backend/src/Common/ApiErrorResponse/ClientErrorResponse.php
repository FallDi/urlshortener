<?php

declare(strict_types=1);

namespace App\Common\ApiErrorResponse;

use OpenApi\Attributes as OA;
use Override;

#[OA\Response(
    response: 'clientError',
    description: 'Client request error',
    content: new OA\JsonContent(
        required: ['message'],
        properties: [
            new OA\Property('message', type: 'string'),
            new OA\Property('violations', type: 'array', items: new OA\Items(ref: ClientErrorResponseViolation::class)),
        ],
    ),
)]
class ClientErrorResponse extends BaseErrorResponse
{
    /**
     * @param ClientErrorResponseViolation[] $violations
     */
    public function __construct(
        protected string $message,
        protected ?array $trace,
        private readonly array $violations = [],
    ) {
        parent::__construct($this->message, $this->trace);
    }

    #[Override]
    public function toArray(): array
    {
        $array = parent::toArray();

        if ($this->violations) {
            $array['violations'] = $this->violations;
        }

        return $array;
    }
}
