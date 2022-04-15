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
        if (!array_key_exists($code, Response::$statusTexts)) {
            return new Response('Invalid status code', Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'code' => $code,
            'text' => Response::$statusTexts[$code],
        ], (int) $code);
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
            summary: 'Return the given status code',
            parameters: [
                new Parameter('code', 'path', 'Wanted status code', true),
            ]
        );
        $collectionItem = new PathItem(
            get: $operation->withOperationId('getStatusCode'),
            put: $operation->withOperationId('putStatusCode'),
            post: $operation->withOperationId('postStatusCode'),
            delete: $operation->withOperationId('deleteStatusCode'),
            patch: $operation->withOperationId('patchStatusCode')
        );
        $openApi->getPaths()->addPath('/status/{code}', $collectionItem);
    }
}
