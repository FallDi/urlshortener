<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * @see https://symfony.com/doc/current/reference/configuration/framework.html
 */
return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'secret' => '%env(string:APP_SECRET)%',
    ]);
};
