<?php

declare(strict_types=1);

namespace App;

use OpenApi\Annotations\OpenApi;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: OpenApi::VERSION_3_0_0)]
#[OA\Info(version: '1.0.0', title: 'Url shortener API')]
#[OA\Server(
    url: 'http://localhost:8182',
)]
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
