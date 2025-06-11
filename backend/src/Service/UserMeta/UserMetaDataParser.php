<?php

declare(strict_types=1);

namespace App\Service\UserMeta;

use DeviceDetector\DeviceDetector;

readonly class UserMetaDataParser
{
    private const string OS_UNKNOWN = 'unknown';

    private const string BROWSER_UNKNOWN = 'unknown';

    public function __construct(private DeviceDetector $deviceDetector)
    {

    }

    public function parse(UserMetaDataRaw $userMetaDataRaw): UserMetaDataParsed
    {
        if ($userMetaDataRaw->userAgent) {
            $this->deviceDetector->setUserAgent($userMetaDataRaw->userAgent);
            $this->deviceDetector->parse();
            $os = $this->deviceDetector->getOs('name') ?: self::OS_UNKNOWN;
            $browser = $this->deviceDetector->getClient('name') ?: self::BROWSER_UNKNOWN;
        } else {
            $os = self::OS_UNKNOWN;
            $browser = self::BROWSER_UNKNOWN;
        }

        return new UserMetaDataParsed($os, $browser);
    }
}
