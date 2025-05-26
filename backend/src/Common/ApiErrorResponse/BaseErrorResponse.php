<?php

declare(strict_types=1);

namespace App\Common\ApiErrorResponse;

abstract class BaseErrorResponse
{
    /**
     * @param array|null $trace Exception trace, just for debug purpose
     */
    public function __construct(protected string $message, protected ?array $trace)
    {
    }

    public function toArray(): array
    {
        $array = [
            'message' => $this->message,
        ];

        if ($this->trace) {
            $array['trace'] = $this->trace;
        }

        return $array;
    }
}
