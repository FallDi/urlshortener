<?php

declare(strict_types=1);

namespace App\Service\Url;

use InvalidArgumentException;

readonly class BaseConverter
{
    public const string SHORT_URL_ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';

    /**
     * Convert base10 number to any base, base specified by alphabet
     *
     * @see https://en.wikipedia.org/wiki/Positional_notation#Base_conversion
     * @see https://en.wikipedia.org/wiki/Hexadecimal#Division-remainder_in_source_base
     */
    public function base10ToBaseShortUrl(int $decimalNumber): string
    {
        if ($decimalNumber <= 0) {
            throw new InvalidArgumentException('Argument $decimalNumber must be positive');
        }

        $alphabetLength = strlen(self::SHORT_URL_ALPHABET);
        $result = '';

        while ($decimalNumber > 0) {
            $remainder = $decimalNumber % $alphabetLength;
            $decimalNumber = ($decimalNumber - $remainder) / $alphabetLength;
            $result = self::SHORT_URL_ALPHABET[$remainder] . $result;
        }

        return $result;
    }

    public function baseShortUrlToBase10(string $baseAlphabetNumber): int
    {
        $alphabetLength = strlen(self::SHORT_URL_ALPHABET);
        $decimalNumber = 0;
        $position = 0;

        while ($baseAlphabetNumber !== '') {
            $lastDigit = $baseAlphabetNumber[-1];
            $decimalNumber += ($alphabetLength ** $position) * strpos(self::SHORT_URL_ALPHABET, $lastDigit);

            ++$position;
            $baseAlphabetNumber = substr($baseAlphabetNumber, 0, -1);
        }

        return $decimalNumber;
    }
}
