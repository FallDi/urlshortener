<?php

declare(strict_types=1);

use Symfony\Config\DoctrineMigrationsConfig;

/**
 * @see https://symfony.com/bundles/DoctrineMigrationsBundle/current/index.html#configuration
 */
return static function (DoctrineMigrationsConfig $doctrine): void {
    $doctrine->migrationsPath('App\Migrations\MariaDb', '%kernel.project_dir%/migrations/mariadb');

    // Entity manager (and related connection) to use for the migrations
    $doctrine->em('em_migrations');
};
