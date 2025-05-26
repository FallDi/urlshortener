<?php

declare(strict_types=1);

namespace App\UseCase\ViewShortUrl;

readonly class ViewShortUrlCommand
{
    public function __construct(public string $shortUrlId)
    {
    }
}
