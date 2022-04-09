<?php

declare(strict_types=1);

namespace App\Controller\API;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\Dto\CookieRequestPayload;
use App\Dto\Errors\FormErrorItemRfc7807DTO;
use App\Dto\Errors\FormErrorRfc7807DTO;
use ArrayObject;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CookieController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/cookies', name: 'api.cookies.get', methods: ['GET'])]
    public function getCookies(Request $request): Response
    {
        return $this->json([
            'cookies' => array_filter($request->cookies->all(), static fn ($key) => $key !== 'sf_redirect', ARRAY_FILTER_USE_KEY),
        ], Response::HTTP_OK);
    }

    #[Route('/cookies', name: 'api.cookies.new', methods: ['POST'])]
    public function newCookie(Request $request): Response
    {
        try {
            /** @var CookieRequestPayload $requestPayload */
            $requestPayload = $this->serializer->deserialize(
                $request->getContent(),
                CookieRequestPayload::class,
                'json',
            );
            $errors = $this->validator->validate($requestPayload);
            if ($errors->count() > 0) {
                return $this->json(
                    $this->createErrorFromValidation($errors),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $response = $this->redirectToRoute('api.cookies.get');
            $response->headers->setCookie($requestPayload->toCookie());
            return $response;
        } catch (MissingConstructorArgumentsException) {
            return $this->json(
                $this->createErrorFromSerialization(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/cookies/{key}', name: 'api.cookies.update', methods: ['PATCH'])]
    public function updateCookie(Request $request, string $key): Response
    {
        if (! $request->cookies->has($key)) {
            return $this->json(sprintf('No cookie found for key "%s"', $key), Response::HTTP_NOT_FOUND);
        }
        $requestPayload = new CookieRequestPayload($key, $request->getContent());
        $response = $this->json($requestPayload, Response::HTTP_OK);
        $response->headers->setCookie($requestPayload->toCookie());
        return $response;
    }

    #[Route('/cookies/{key}', name: 'api.cookies.delete', methods: ['DELETE'])]
    public function deleteCookie(Request $request, string $key): Response
    {
        if (! $request->cookies->has($key)) {
            return $this->json(sprintf('No cookie found for key "%s"', $key), Response::HTTP_NOT_FOUND);
        }
        $response = $this->json(sprintf('The cookie with key "%s" is deleted', $key), Response::HTTP_OK);
        $response->headers->clearCookie($key);
        return $response;
    }

    public static function setupOpenApiDocumentation(OpenApi $openApi): void
    {
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Cookie'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'key' => [
                    'type' => 'string',
                    'example' => 'freeform',
                ],
                'value' => [
                    'type' => 'string',
                    'example' => 'example',
                ],
            ],
        ]);

        $schemas['Cookies'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'cookies' => [
                    'type' => 'object',
                    'properties' => [
                        'key' => [
                            'type' => 'string',
                            'example' => 'value',
                        ],
                    ],
                ],
            ],
        ]);

        $collectionItem = new PathItem(
            get: new Operation(
                operationId: 'getCookie',
                tags: ['Cookies'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Cookies',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Returns cookie data.'
            ),
            post: new Operation(
                operationId: 'postCookie',
                tags: ['Cookies'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Cookies',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Sets cookie as provided by the content and redirects to cookie list.',
                requestBody: new RequestBody(
                    description: 'Generate new Cookie',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Cookie',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $singleItem = new PathItem(
            delete: new Operation(
                operationId: 'deleteCookie',
                tags: ['Cookies'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Cookies',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Delete a cookie',
                parameters: [
                    new Parameter('key', 'path', 'The key of your cookie', true),
                ]
            ),
            patch: new Operation(
                operationId: 'patchCookie',
                tags: ['Cookies'],
                responses: [
                    '200' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Cookies',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Update cookie data.',
                parameters: [
                    new Parameter('key', 'path', 'The key of your cookie', true),
                ],
                requestBody: new RequestBody(
                    description: 'Update the cookie',
                    content: new ArrayObject([
                        'text/plain' => [
                            'schema' => [
                                'body' => [
                                    'type' => 'string',
                                    'example' => 'freeform',
                                ],
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/cookies', $collectionItem);
        $openApi->getPaths()->addPath('/api/cookies/{key}', $singleItem);
    }

    #[Pure] private function createErrorFromSerialization(): FormErrorRfc7807DTO
    {
        $mainDto = new FormErrorRfc7807DTO();
        $mainDto->title = 'Bad Request';
        $mainDto->type = 'Missing JSON node';

        return $mainDto;
    }

    private function createErrorFromValidation(ConstraintViolationListInterface $violations): FormErrorRfc7807DTO
    {
        $mainDto = new FormErrorRfc7807DTO();
        $mainDto->title = 'Bad Request';
        $mainDto->type = 'UI Validation';

        $items = [];
        foreach ($violations as $item) {
            $dto = new FormErrorItemRfc7807DTO();
            $dto->propertyPath = $item->getPropertyPath();
            $dto->message = $item->getMessage();
            $items[] = $dto;
        }

        $mainDto->violations = $items;

        return $mainDto;
    }
}
