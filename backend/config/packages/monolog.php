<?php

declare(strict_types=1);

use Psr\Log\LogLevel;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog): void {
    $monolog->handler('main')
        ->type('stream')
         // log to var/logs/(environment).log
        ->path('%kernel.logs_dir%/%kernel.environment%.log')
         // log *all* messages (LogLevel::DEBUG is lowest level)
        ->level(LogLevel::DEBUG)
        ->channels()->elements(['!event', '!request', '!doctrine']);

    // additional 12factor https://12factor.net/logs
    $monolog->handler('stdout')
        ->type('stream')
        ->path('php://stdout')
        ->level(LogLevel::DEBUG)
        ->channels()->elements(['!event', '!request', '!doctrine']);
};
