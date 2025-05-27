# URL Shortener

## Intro
It's just yet another implementation of URL Shortener based on fresh PHP, Symfony and other packages (`composer outdated --direct` outputs nothing).
In addition, project stores clients user agent info to Redis, which available via CLI command.
Project made just for fun and as boilerplate project example.

## Installation
- Download and install [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Run command `./docker/manager up`

That's it :)

## URL generation approach
Each new URL that is added to the database is assigned a unique, auto-incrementing ID (primary key in RDBMS).
Once the ID is generated, it is converted into a shorter URL using encoding similar to Base64.

## Usage example
* Create URL someway
Via CURL 
```
curl -XPOST 'http://localhost:8182/api/urls?XDEBUG_SESSION=PHPSTORM' \
        -H "Content-Type: application/json" \
        --data '{"url":"https://symfony.com/doc/current/console.html", "expiresAt":"2029-01-01T02:03:04Z"}'

Response: {"shortUrl":"http:\/\/localhost:8182\/api\/urls\/M"}
```
* Visit short url
```
curl -v -XGET http://localhost:8182/api/urls/M?XDEBUG_SESSION=PHPSTORM

HTTP/1.1 302 Found
Cache-Control: no-cache, private
Location: https://symfony.com/doc/current/console.html
```
* Show stats
```
$ ./docker/manager php-fpm-bash                                                                                                0.0s
$ php bin/console app:view-stats
+---------+------------------------+
| OS      | number of created URLs |
+---------+------------------------+
| UNK     | 37                     |
| Windows | 1                      |
+---------+------------------------+
+---------+------------------------+
| Browser | number of created URLs |
+---------+------------------------+
| curl    | 13                     |
| Chrome  | 1                      |
| UNK     | 24                     |
+---------+------------------------+
```

See full API in [backend/openapi.yaml](backend/openapi.yaml)

## Helpful commands
* Run `./docker/manager` to see helpful development commands
```
Usage: ./docker/manager COMMAND

Commands:
  up                                       Start containers
  down                                     Stop and remove containers
  build                                    Build docker services
  composer-install                         Install PHP packages
  composer-bash                            Run bash in composer container
  migrations-up                            Execute SQL migrations
  php-fpm-bash                             Run bash in php-fpm container

Helpful services:
  run-tests                                Run tests
  run-tests-with-coverage                  Run tests with code coverage
  generate-openapi                         Generate OpenAPI
  fix-code-style                           Run code style formatter
  run-pma                                  Run phpMyAdmin on http://localhost:3001
  run-redis-admin                          Run phpRedisAdmin on http://localhost:3002
  xdebug-container-on [container] [mode]   Enables xdebug inside a specific container
  xdebug-container-off [container]         Disables xdebug inside a specific container
```
* Run any SQL query `php backend/bin/console dbal:run-sql 'SELECT * FROM url'`
* Xdebug3 configuration
  * run `docker compose -f ./docker/compose.yml ps`, fetch container name
  * run `./docker/manager $containerName` e.g. `./docker/manager xdebug-container-on docker-php-fpm-1`
  * Configure PHPSTORM or another IDE
  * Test via `curl -XGET http://localhost:8182/api-user/urls/a?XDEBUG_SESSION=PHPSTORM`

## Docs
* Symfony: Environments & Configs & EnvironmentVariables https://symfony.com/doc/current/configuration.html

## Troubleshooting
* Project looks outdated after source code changes
  * Solution: Try to cleanup Symfony DI container cache e.g. `APP_ENV=dev php backend/bin/console cache:clear`
* I do changes in docker compose configuration (e.g. update service or Dockerfile), but changes not updated after 
  `./docker/manager up`
  * Solution: try to build services via `./docker/manager build`
* Project runs slowly on WSL2
    * Solution: To avoid slow IO store project in WSL filesystem `\\wsl$`, instead of using windows drive mounted to WSL `/mnt/`,
  see https://stackoverflow.com/a/67736079/3178453 or https://stackoverflow.com/a/70560669/3178453

## Possible improvements
* Separate DB for `APP_ENV=test`
