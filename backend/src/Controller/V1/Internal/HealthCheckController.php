<?php

declare(strict_types=1);

namespace App\Controller\V1\Internal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines probes to do healthcheck
 * @see https://kubernetes.io/docs/concepts/configuration/liveness-readiness-startup-probes/
 */
class HealthCheckController extends AbstractController
{
    public function startupProbe(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
