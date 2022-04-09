<?php

declare(strict_types=1);

namespace App\Controller\API;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestController extends AbstractController
{
    #[Route('/ip', name: 'api.request.ip', methods: ['GET'])]
    public function getIP(Request $request): Response
    {
        return $this->json([
            'origins' => $request->getClientIps(),
        ]);
    }

    #[Route('/headers', name: 'api.request.headers', methods: ['GET'])]
    public function getHeaders(Request $request): Response
    {
        return $this->json([
            'headers' => array_map(static fn ($entry) => implode('', $entry), $request->headers->all()),
        ]);
    }

    #[Route('/user-agent', name: 'api.request.user_agent', methods: ['GET'])]
    public function getUserAgent(Request $request): Response
    {
        return $this->json([
            'user-agent' => $request->headers->get('User-Agent'),
        ]);
    }

    /**
     * @throws JsonException
     */
    /**
     * @throws JsonException
     */
    #[Route('/anything', name: 'api.request.anything', methods: ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'])]
    public function getAnything(Request $request): Response
    {
        return $this->json([
            'method' => $request->getMethod(),
            'origins' => $request->getClientIps(),
            'headers' => array_map(static fn ($entry) => implode('', $entry), $request->headers->all()),
            'content-type' => $request->getContentType(),
            'raw_body' => $request->getContent(),
            'body' => $request->getContentType() === 'json' ? json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR) : $request->getContent(),
        ]);
    }

    public static function setupOpenApiDocumentation(OpenApi $openApi): void
    {
        $openApi->getPaths()->addPath('/api/ip', new PathItem(
            get: new Operation(
                operationId: 'getIp',
                tags: ['Requests'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'origins' => [
                                            'type' => 'array',
                                            'example' => ['::1'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Returns the requester\'s IP Address.'
            )
        ));

        $openApi->getPaths()->addPath('/api/headers', new PathItem(
            get: new Operation(
                operationId: 'getHeaders',
                tags: ['Requests'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'headers' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'key' => [
                                                    'type' => 'string',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Return the incoming request\'s HTTP headers.'
            )
        ));

        $openApi->getPaths()->addPath('/api/user-agent', new PathItem(
            get: new Operation(
                operationId: 'getUA',
                tags: ['Requests'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'user-agent' => [
                                            'type' => 'string',
                                            'example' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Return the incoming requests\'s User-Agent header.'
            )
        ));

        $anythingOperation = new Operation(
            tags: ['Anything'],
            responses: [
                '200' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'method' => [
                                        'type' => 'string',
                                        'example' => 'GET',
                                    ],
                                    'origins' => [
                                        'type' => 'array',
                                        'example' => ['::1'],
                                    ],
                                    'headers' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'key' => [
                                                'type' => 'string',
                                            ],
                                        ],
                                    ],
                                    'content-type' => [],
                                    'raw_body' => [],
                                    'body' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            summary: 'Returns anything passed in request data.'
        );
        $openApi->getPaths()->addPath('/api/anything', new PathItem(
            get: $anythingOperation->withOperationId('getAnything'),
            put: $anythingOperation->withOperationId('putAnything'),
            post: $anythingOperation->withOperationId('postAnything'),
            delete: $anythingOperation->withOperationId('deleteAnything'),
            patch: $anythingOperation->withOperationId('patchAnything')
        ));
    }
}
