<?php

declare(strict_types=1);

namespace App\Tests\Application;

use Override;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseWebTestCase extends WebTestCase
{
    #[Override]
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        if (!array_key_exists('HTTP_HOST', $server)) {
            $server['HTTP_HOST'] = $_ENV['APP_URL'];
        }

        return parent::createClient($options, $server);
    }
}
