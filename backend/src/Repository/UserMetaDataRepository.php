<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\RedisClient\RedisStatsClient;
use App\Service\UserMeta\UserMetaDataParsed;

readonly class UserMetaDataRepository
{
    private const string KEY_OS = 'os';

    private const string KEY_BROWSER = 'browser';

    public function __construct(private RedisStatsClient $statsClient)
    {

    }

    /**
     * @internal Possible improvement: handle via background worker to handle http response faster
     */
    public function save(UserMetaDataParsed $userMetaData): void
    {
        $this->statsClient->hincrby(self::KEY_OS, $userMetaData->os, 1);
        $this->statsClient->hincrby(self::KEY_BROWSER, $userMetaData->browser, 1);
    }

    public function getOsData(): array
    {
        return $this->statsClient->hgetall(self::KEY_OS);
    }

    public function getBrowserData(): array
    {
        return $this->statsClient->hgetall(self::KEY_BROWSER);
    }
}
