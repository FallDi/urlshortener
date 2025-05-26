<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

# This file is the entry point to configure your own services.
# Files in the 'packages/' subdirectory configure your dependencies.

# Put parameters here that don't need to change on each machine where the app is deployed
# https://symfony.com/doc/current/best_practices.html#use-parameters-for-application-configuration

use App\Common\Clock;
use App\Common\RedisClient\RedisCacheClient;
use App\Common\RedisClient\RedisStatsClient;
use App\EventSubscriber\JsonResponseSubscriber;
use DeviceDetector\DeviceDetector;
use Psr\Clock\ClockInterface;

return static function (ContainerConfigurator $container): void {
    // https://symfony.com/doc/current/performance.html#dump-the-service-container-into-a-single-file
    $container->parameters()->set('.container.dumper.inline_factories', true);

    // default configuration for services
    $services = $container->services()
        ->defaults()
        ->autowire() // Automatically injects dependencies in your services.
        ->autoconfigure(); // Automatically registers your services as commands, event subscribers, etc.

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below

    $services->alias(ClockInterface::class, Clock::class);

    $services->set(JsonResponseSubscriber::class)
        ->arg('$debug', env('APP_DEBUG')->bool());

    $services->set(RedisCacheClient::class)
        ->arg(
            '$parameters',
            [
                // https://github.com/predis/predis/wiki/Connection-Parameters
                'schema' => 'tcp',
                'host' => '%env(string:REDIS_HOST)%',
                'port' => '%env(int:REDIS_PORT)%',
                'database' => '%env(int:REDIS_DB_CACHE)%',
            ],
        );

    $services->set(RedisStatsClient::class)
        ->arg(
            '$parameters',
            [
                // https://github.com/predis/predis/wiki/Connection-Parameters
                'schema' => 'tcp',
                'host' => '%env(string:REDIS_HOST)%',
                'port' => '%env(int:REDIS_PORT)%',
                'database' => '%env(int:REDIS_DB_STATS)%',
            ],
        );

    // External dependencies (/vendor/...) for autowiring
    $services->set(DeviceDetector::class);
};
