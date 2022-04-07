<?php

declare(strict_types=1);

namespace App\Controller\API;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    #[Route('/status/{code}', name: 'api.status.code', methods: ['DELETE', 'GET', 'POST', 'PUT', 'PATCH'])]
    public function getStatusCode(string $code): Response
    {
        if (! array_key_exists($code, Response::$statusTexts)) {
            return new Response('Invalid status code', Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'code' => $code,
            'text' => Response::$statusTexts[$code],
        ], $code);
    }

    public static function setupOpenApiDocumentation(OpenApi $openApi): void
    {
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['StatusCode'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => '200',
                ],
                'text' => [
                    'type' => 'string',
                    'example' => 'OK',
                ],
            ],
        ]);
        $operation = new Operation(
            tags: ['Status codes'],
            responses: [
                '*' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/StatusCode',
                            ],
                        ],
                    ],
                ],
            ],
            summary: 'Return status code or random status code if more than one are given',
            parameters: [
                new Parameter('code', 'path', '', true),
            ]
        );
        $collectionItem = new PathItem(
            get: $operation,
            put: $operation,
            post: $operation,
            delete: $operation,
            patch: $operation
        );
        $openApi->getPaths()->addPath('/api/status/{code}', $collectionItem);
    }
}
