<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Controller\Request\CreateUrlRequest;
use App\Service\UserMeta\UserMetaDataRaw;
use App\UseCase\CreateShortUrl\CreateShortUrlCommand;
use App\UseCase\CreateShortUrl\CreateShortUrlUseCase;
use App\UseCase\CreateShortUrl\PastExpiresAtException;
use App\UseCase\ViewShortUrl\ShortUrlNotFoundException;
use App\UseCase\ViewShortUrl\ViewShortUrlCommand;
use App\UseCase\ViewShortUrl\ViewShortUrlUseCase;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UrlController extends AbstractController
{
    public function __construct(
        private readonly CreateShortUrlUseCase $createShortUrlUseCase,
        private readonly ViewShortUrlUseCase $viewShortUrlUseCase,
    ) {
    }

    #[OA\Post(
        path: '/api/urls',
        summary: 'Creates short URL',
        requestBody: new OA\RequestBody(
            description: 'Input data format',
            content: new OA\JsonContent(
                required: ['url'],
                properties: [
                    new OA\Property('url', description: 'Url to be shorted', type: 'string'),
                    new OA\Property('expiresAt', description: 'Url expiration datetime', type: 'string', format: 'date-time'),
                ],
                type: 'object',
            ),
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Short URL created successfully',
        content: new OA\JsonContent(
            required: ['shortUrl'],
            properties: [
                new OA\Property('shortUrl', description: 'Short URL to be shorted', type: 'string'),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(ref: '#/components/responses/clientError', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[OA\Response(ref: '#/components/responses/serverError', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    public function create(
        Request $request,
        #[MapRequestPayload] CreateUrlRequest $createUrlRequest,
    ): Response {
        $command = new CreateShortUrlCommand(
            $createUrlRequest->url,
            $createUrlRequest->expiresAt ? new DateTimeImmutable($createUrlRequest->expiresAt) : null,
            new UserMetaDataRaw($request->getClientIp(), $request->headers->get('User-Agent')),
        );
        try {
            $shortUrl = $this->createShortUrlUseCase->handle($command);
        } catch (PastExpiresAtException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return new JsonResponse(['shortUrl' => $shortUrl], Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/urls/{id}',
        summary: 'View short URL',
        parameters: [
            new OA\PathParameter(name: 'id', required: true, schema: new OA\Schema(type: 'string')),
        ],
    )]
    #[OA\Response(
        response: Response::HTTP_FOUND,
        description: 'Short URL created successfully',
    )]
    #[OA\Response(
        ref: '#/components/responses/clientError',
        response: Response::HTTP_NOT_FOUND,
        description: 'URL not found',
    )]
    #[OA\Response(ref: '#/components/responses/serverError', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    public function view(string $id): RedirectResponse
    {
        $command = new ViewShortUrlCommand($id);

        try {
            $url = $this->viewShortUrlUseCase->handle($command);
        } catch (ShortUrlNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return ($this->redirect($url))->setContent(null);
    }
}
