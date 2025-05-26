<?php

declare(strict_types=1);

use Symfony\Config\DoctrineConfig;

/**
 * @see https://symfony.com/doc/current/reference/configuration/doctrine.html
 * @see https://symfony.com/doc/current/doctrine/multiple_entity_managers.html
 */
return static function (DoctrineConfig $doctrine): void {
    // DBAL: Connections
    $doctrine->dbal()->defaultConnection('app');

    $doctrine->dbal()
        ->connection('app')
        ->url('%env(string:DATABASE_APP_URL)%');

    $doctrine->dbal()
        ->connection('migrations')
        ->url('%env(string:DATABASE_MIGRATIONS_URL)%');

    // ORM
    $doctrine->orm()->controllerResolver()->autoMapping(false);

    // ORM: Entity Managers
    $doctrine->orm()->defaultEntityManager('em_app');

    $appEntityManager = $doctrine->orm()->entityManager('em_app');
    $appEntityManager->connection('app');
    $appEntityManager->mapping('Main')
        ->isBundle(false)
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('Main');
    // https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/caching.html#query-cache
    $appEntityManager->queryCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
    // https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/caching.html#metadata-cache
    $appEntityManager->metadataCacheDriver()->type('pool')->pool('doctrine.system_cache_pool');
    // https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/caching.html#result-cache
    $appEntityManager->resultCacheDriver()->type('pool')->pool('doctrine.result_cache_pool');

    $migrationsEntityManager = $doctrine->orm()->entityManager('em_migrations');
    $migrationsEntityManager->connection('migrations');
    $migrationsEntityManager->mapping('Migration')
        ->isBundle(false)
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('Migration')
    ;
};
