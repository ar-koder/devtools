<?php

declare(strict_types=1);

namespace App\Controller\API;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use NicoVerbruggen\ImageGenerator\ImageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ImageController extends AbstractController
{
    #[Route('/placeholder/{dimensions}.{format}', name: 'api.image.placeholder', methods: ['GET'], defaults: ['format' => 'png'])]
    public function getPlaceholder(Request $request, string $dimensions, string $format = 'png'): Response
    {
        [$width, $height] = self::parseDimensions($dimensions);

        $bgColor = $request->query->get('bgColor', '#2b2d42');
        $bgColor = $bgColor === 'random' ? null : $bgColor;

        $textColor = $request->query->get('textColor', '#edf2f4');
        $textColor = $textColor === 'random' ? null : $textColor;

        $projectRoot = $this->getParameter('kernel.project_dir');

        ob_start();
        (new ImageGenerator(
            $width . 'x' . $height,
            $textColor,
            $bgColor,
            $projectRoot . '/var/fonts/Noto.ttf',
            min($width, $height) * 0.125,
            2
        ))
            ->generate($request->query->get('text'), 'php://output')
        ;
        $image_content = ob_get_clean();

        return $this->renderImage($image_content, $format);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/space/{dimensions}.{format}', name: 'api.image.space', defaults: ['format' => 'png'], methods: ['GET'])]
    #[Route('/space/{category}/{dimensions}.{format}', name: 'api.image.space_categorize', defaults: ['category' => 'random', 'format' => 'png'], methods: ['GET'])]
    public function getSpace(Request $request, string $dimensions, string $category = 'random', string $format = 'png'): Response
    {
        [$width, $height] = self::parseDimensions($dimensions);
        $url = sprintf('https://api.lorem.space/image%s?w=%s&h=%s', $category && $category !== 'random' ? '/'.$category : '', $width ? $width : '', $height ? $height : '');
        $client = HttpClient::create();
        $response = $client->request('GET', $url);

        return $this->renderImage($response->getContent(), $format);
    }

    public static function setupOpenApiDocumentation(OpenApi $openApi): void
    {
        $openApi->getPaths()->addPath('/api/placeholder/{dimensions}.{format}', new PathItem(
            get: new Operation(
                tags: ['Images'],
                operationId: 'getPlaceholder',
                responses: [
                    '200' => [
                        'content' => [
                            'image/*' => [],
                        ],
                    ],
                ],
                summary: 'Return an image with the specified dimensions.',
                parameters: [
                    new Parameter(name: 'dimensions', in: 'path', description: 'a dimensions string (WxH)', required: true, example: '200x200'),
                    new Parameter(name: 'format', in: 'path', description: 'return format of image (png, jpg, gif or webp)', required: true, schema: ['type' => 'string', 'enum' => ['png', 'jpg', 'gif', 'webp']], example: 'png'),
                    new Parameter(name: 'bgColor', in: 'query', description: 'background color in #FFFFFF format', required: false, example: '#FFF'),
                    new Parameter(name: 'textColor', in: 'query', description: 'text color in #FFFFFF format', required: false, example: '#000'),
                    new Parameter(name: 'text', in: 'query', description: 'a custom text value', required: false, example: 'Example'),
                ]
            )
        ));

        $openApi->getPaths()->addPath('/api/space/{dimensions}.{format}', new PathItem(
            get: new Operation(
                operationId: 'getSpaceholder',
                tags: ['Images'],
                responses: [
                    '200' => [
                        'content' => [
                            'image/*' => [],
                        ],
                    ],
                ],
                summary: 'Return an image from lorem.space with the specified dimensions.',
                parameters: [
                    new Parameter(name: 'dimensions', in: 'path', description: 'a dimensions string (WxH), if one is 0 only scale is applied', required: true, example: '200x200'),
                    new Parameter(name: 'format', in: 'path', description: 'Return format of image (png, jpg, gif or webp)', required: true, schema: ['type' => 'string', 'enum' => ['png', 'jpg', 'gif', 'webp']], example: 'png'),
                ]
            )
        ));

        $openApi->getPaths()->addPath('/api/space/{category}/{dimensions}.{format}', new PathItem(
            get: new Operation(
                operationId: 'getCategorizedSpaceholder',
                tags: ['Images'],
                responses: [
                    '200' => [
                        'content' => [
                            'image/*' => [],
                        ],
                    ],
                ],
                summary: 'Return an image from lorem.space with the specified dimensions and category.',
                parameters: [
                    new Parameter(name: 'category', in: 'path', description: 'the category name', required: true, schema: ['type' => 'string', 'enum' => ['movie', 'game', 'album', 'book', 'face', 'fashion', 'shoes', 'watch', 'furniture', 'pizza', 'burger', 'drink', 'car', 'house', 'random']], example: 'random'),
                    new Parameter(name: 'dimensions', in: 'path', description: 'a dimensions string (WxH), if one is 0 only scale is applied', required: true, example: '200x200'),
                    new Parameter(name: 'format', in: 'path', description: 'Return format of image (png, jpg, gif or webp)', required: true, schema: ['type' => 'string', 'enum' => ['png', 'jpg', 'gif', 'webp']], example: 'png'),
                ]
            )
        ));
    }

    private function renderImage(string $content, string $format = 'png'): Response
    {
        ob_start();
        $image = imagecreatefromstring($content);
        $mimeType = null;
        switch ($format) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image);
                $mimeType = 'image/jpeg';
                break;
            case 'gif':
                imagegif($image);
                $mimeType = 'image/gif';
                break;
            case 'png':
                imagepng($image);
                $mimeType = 'image/png';
                break;
            case 'webp':
                imagepalettetotruecolor($image);
                imagewebp($image);
                $mimeType = 'image/webp';
                break;
        }
        $content = ob_get_clean();

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, uniqid('', true). ".${format}");
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $mimeType);
        $response->setContent($content);

        return $response;
    }

    private static function parseDimensions(string $dimensions): array
    {
        $dimensions = explode('x', $dimensions);
        if (count($dimensions) === 1) {
            $dimensions[1] = $dimensions[0];
        }
        $dimensions = implode('x', $dimensions);
        return explode('x', $dimensions);
    }
}
