<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        // https://symfony.com/doc/current/reference/configuration/framework.html#test
        'test' => true,
    ]);
};
