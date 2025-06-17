<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Common\ApiErrorResponse\ClientErrorResponse;
use App\Common\ApiErrorResponse\ClientErrorResponseViolation;
use App\Common\ApiErrorResponse\ServerErrorResponse;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

/**
 * Custom handling of errors to respond in JSON format
 * @see https://symfony.com/doc/current/controller/error_pages.html#working-with-the-kernel-exception-event
 */
class JsonResponseSubscriber implements EventSubscriberInterface
{
    /**
     * Error messages from such exception can be safely displayed to end user without any sensitive info disclosure
     */
    private const array SAFE_ERROR_MESSAGE_EXCEPTIONS = [
        NotFoundHttpException::class,
        BadRequestHttpException::class,
        UnprocessableEntityHttpException::class,
    ];

    public function __construct(private readonly bool $debug)
    {
    }

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -10]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previousException = $exception->getPrevious();
        $trace = $this->debug ? $exception->getTrace() : null;

        if ($exception instanceof HttpExceptionInterface) {
            $httpCode = $exception->getStatusCode();

            if ($previousException instanceof ValidationFailedException) {
                // Customize validator (\Assert\xxx) error message
                // @see https://symfony.com/doc/current/controller.html#automatic-mapping-of-the-request
                $violations = [];

                foreach ($previousException->getViolations() as $violation) {
                    $violations[] = new ClientErrorResponseViolation(
                        $violation->getPropertyPath(),
                        $violation->getMessage(),
                    );
                }

                $apiResponse = new ClientErrorResponse('Invalid request', $trace, $violations);
            } else {
                $apiResponse = new ClientErrorResponse(
                    $this->getResponseMessage(
                        $this->debug || in_array(get_class($exception), self::SAFE_ERROR_MESSAGE_EXCEPTIONS, true),
                        $exception,
                    ),
                    $trace,
                );
            }
        } else {
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $apiResponse = new ServerErrorResponse($this->getResponseMessage($this->debug, $exception), $trace);
        }

        // If your listener calls setResponse() on the ExceptionEvent event,
        // propagation will be stopped and the response will be sent to the client.
        $event->setResponse(new JsonResponse($apiResponse->toArray(), $httpCode));
    }

    private function getResponseMessage(bool $isShowRealMessage, Throwable $exception): string
    {
        return $isShowRealMessage ? $exception->getMessage() : 'Internal server error';
    }
}
