<?php

declare(strict_types=1);

namespace App\Controller\API;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class GeneratorController extends AbstractController
{
    #[Route('/uuid', name: 'api.generator.uuid', methods: ['GET'])]
    public function getUUID(): Response
    {
        return $this->json([
            'uuid_v4' => Uuid::v4()->toRfc4122(),
            'uuid_v6' => Uuid::v6()->toRfc4122(),
        ], Response::HTTP_OK);
    }

    #[Route('/encode/{decoded}', name: 'api.generator.encode_b64', methods: ['GET'])]
    public function getEncodeB64(string $decoded): Response
    {
        return $this->json(base64_encode($decoded), Response::HTTP_OK);
    }

    #[Route('/decode/{encoded}', name: 'api.generator.decode_b64', methods: ['GET'])]
    public function getDecodeB64(string $encoded): Response
    {
        return $this->json(base64_decode($encoded), Response::HTTP_OK);
    }

    public static function setupOpenApiDocumentation(OpenApi $openApi): void
    {
        $openApi->getPaths()->addPath('/api/uuid', new PathItem(
            get: new Operation(
                operationId: 'getUUIDs',
                tags: ['Dynamic data'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'uuid_v4' => [
                                            'type' => 'string',
                                            'example' => '00b8db1e-546b-4f9a-945f-6d1615f471b9',
                                        ],
                                        'uuid_v6' => [
                                            'type' => 'string',
                                            'example' => '1ecb6569-2e67-6fde-a911-0fb751f58ce9',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Return a UUID4 and a UUID6.'
            )
        ));

        $openApi->getPaths()->addPath('/api/encode/{decoded}', new PathItem(
            get: new Operation(
                operationId: 'getEncoded',
                tags: ['Dynamic data'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'body' => [
                                        'type' => 'string',
                                        'example' => 'ZGVjb2RlZA==',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Return a base64 encoded string.',
                parameters: [
                    new Parameter(name: 'decoded', in: 'path', description: 'string', required: true, example: 'decoded'),
                ]
            )
        ));

        $openApi->getPaths()->addPath('/api/decode/{encoded}', new PathItem(
            get: new Operation(
                operationId: 'getDecoded',
                tags: ['Dynamic data'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'body' => [
                                        'type' => 'string',
                                        'example' => 'decoded',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Return a base64 decoded string.',
                parameters: [
                    new Parameter(name: 'encoded', in: 'path', description: 'A base64 encoded string', required: true, example: 'ZGVjb2RlZA=='),
                ]
            )
        ));
    }
}
