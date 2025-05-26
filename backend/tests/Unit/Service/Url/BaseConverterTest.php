<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Url;

use App\Service\Url\BaseConverter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversMethod(BaseConverter::class, 'base10ToBaseShortUrl')]
#[CoversMethod(BaseConverter::class, 'baseShortUrlToBase10')]
class BaseConverterTest extends TestCase
{
    public static function providerBase10ToBaseShortUrl(): array
    {
        return [
            'negative' => [
                'decimal' => -100,
                'expected' => '...EXCEPTION_POSITIVE_ARGUMENT...',
            ],
            'zero' => [
                'decimal' => 0,
                'expected' => '...EXCEPTION_POSITIVE_ARGUMENT...',
            ],
            'one' => [
                'decimal' => 1,
                'expected' => 'b',
            ],
            'ten' => [
                'decimal' => 10,
                'expected' => 'k',
            ],
            'maximum possible decimal' => [
                'decimal' => PHP_INT_MAX,
                'expected' => 'h----------',
            ],
        ];
    }

    #[DataProvider('providerBase10ToBaseShortUrl')]
    public function testBase10ToBaseShortUrl(int $decimal, string $expected): void
    {
        if ($expected === '...EXCEPTION_POSITIVE_ARGUMENT...') {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('Argument $decimalNumber must be positive');
            new BaseConverter()->base10ToBaseShortUrl($decimal);
        } else {
            $this->assertSame($expected, new BaseConverter()->base10ToBaseShortUrl($decimal));
        }
    }

    public static function providerBaseShortUrlToBase10(): array
    {
        return [
            'one' => [
                'baseAlphabetNumber' => 'b',
                'expected' => 1,
            ],
            'ten' => [
                'baseAlphabetNumber' => 'k',
                'expected' => 10,
            ],
            'maximum possible decimal' => [
                'baseAlphabetNumber' => 'h----------',
                'expected' => PHP_INT_MAX,
            ],
        ];
    }

    #[DataProvider('providerBaseShortUrlToBase10')]
    public function testBaseShortUrlToBase10(string $baseAlphabetNumber, int $expected): void
    {
        $this->assertSame($expected, new BaseConverter()->baseShortUrlToBase10($baseAlphabetNumber));
    }
}
