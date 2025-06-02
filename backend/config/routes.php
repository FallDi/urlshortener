<?php

declare(strict_types=1);

use App\Controller\V1\UrlController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @see https://symfony.com/doc/current/routing.html
 */
return static function (RoutingConfigurator $routes): void {
    $routes->add('short-url-create', '/api/v1/urls')
        ->controller([UrlController::class, 'create'])
        ->methods(['POST']);

    $routes->add('short-url-view', '/api/v1/urls/{id}')
        ->controller([UrlController::class, 'view'])
        ->methods(['GET']);
};
