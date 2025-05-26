<?php

//Copy this file to config.inc.php and make changes to that file to customize your configuration.

$config = [
    'servers' => [
        [
            'name' => 'Cache',
            'host' => 'redis.app.local',
            'port' => 6379,
            'filter' => '*',
            'scheme' => 'tcp',
            'path' => '',
            'db' => 0,
            'databases' => 1,
        ],
        [
            'name' => 'Stats',
            'host' => 'redis.app.local',
            'port' => 6379,
            'filter' => '*',
            'scheme' => 'tcp',
            'path' => '',
            'db' => 1,
            'databases' => 1,
        ],
    ],

    'seperator' => ':',

    // Use HTML form/cookie-based auth instead of HTTP Basic/Digest auth
    'cookie_auth' => false,

    // You can ignore settings below this point.
    'maxkeylen' => 100,
    'count_elements_page' => 100,

    // Use the old KEYS command instead of SCAN to fetch all keys.
    'keys' => false,

    // How many entries to fetch using each SCAN command.
    'scansize' => 1000,
];
