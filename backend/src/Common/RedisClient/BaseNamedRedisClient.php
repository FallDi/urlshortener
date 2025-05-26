<?php

declare(strict_types=1);

namespace App\Common\RedisClient;

use Predis\Client;

/**
 * To simplify autowire multiple instances of same type (Predis\Client)
 * we create empty Predis child class per each client (connection)
 *
 * @see https://tomasvotruba.com/blog/how-to-autowire-multiple-instances-of-same-type-in-symfony-laravel
 */
abstract class BaseNamedRedisClient extends Client
{
}
