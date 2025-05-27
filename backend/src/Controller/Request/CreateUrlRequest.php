<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Entity\Url;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateUrlRequest
{
    public const string EXPIRES_AT_FORMAT = 'Y-m-d\\TH:i:s\\Z';

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max:Url::MAX_URL_LENGTH)]
        #[Assert\Url(requireTld: true)]
        public string $url,
        #[Assert\DateTime(format: self::EXPIRES_AT_FORMAT)]
        public ?string $expiresAt,
    ) {
    }
}
