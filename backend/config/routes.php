<?php

declare(strict_types=1);

use App\Common\RouteNames;
use App\Controller\V1\Internal\HealthCheckController;
use App\Controller\V1\Public\UrlController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @see https://symfony.com/doc/current/routing.html
 */
return static function (RoutingConfigurator $routes): void {
    $routes->add('public-short-url-create', '/api/public/v1/urls')
        ->controller([UrlController::class, 'create'])
        ->methods(['POST']);

    $routes->add(RouteNames::PUBLIC_SHORT_URL_VIEW, '/api/public/v1/urls/{id}')
        ->controller([UrlController::class, 'view'])
        ->methods(['GET']);

    $routes->add('internal-probes-startup', '/api/internal/v1/probes/startup')
        ->controller([HealthCheckController::class, 'startupProbe'])
        ->methods(['GET']);
};
