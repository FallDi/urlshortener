<?php

declare(strict_types=1);

use App\Common\RedisClient\RedisCacheClient;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    // default cache pool https://symfony.com/doc/current/cache.html#cache-app-system
    $framework->cache()
        ->system('cache.adapter.system');

    // custom cache pools
    $framework->cache()
        ->pool('doctrine.system_cache_pool')
        ->adapters(['cache.system']);

    $framework->cache()
        ->pool('doctrine.result_cache_pool')
        ->adapters(['cache.adapter.redis'])
        ->provider(RedisCacheClient::class);
};
