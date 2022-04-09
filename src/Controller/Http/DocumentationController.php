<?php

declare(strict_types=1);

namespace App\Controller\Http;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DocumentationController extends AbstractController
{
    private OpenApi $openApi;

    public function __construct(OpenApiFactoryInterface $openApiFactory)
    {
        $this->openApi = $openApiFactory([]);
    }

    #[Route('/', name: 'app.homepage')]
    public function index(): Response
    {
        return $this->render('http_documentation/index.html.twig', [
            'controller_name' => 'HttpDocumentationController',
        ]);
    }

    #[Route('/{tag}', name: 'app.tag')]
    public function tag(string $tag): Response
    {
        $paths = [];
        /**
         * @var string $path
         * @var PathItem $pathItem
         */
        foreach ($this->openApi->getPaths()->getPaths() as $path => $pathItem) {
            $operations = [];

            if ($operation = $pathItem->getGet()) {
                $operations[] = $operation;
            }
            if ($operation = $pathItem->getPost()) {
                $operations[] = $operation;
            }
            if ($operation = $pathItem->getPatch()) {
                $operations[] = $operation;
            }
            if ($operation = $pathItem->getPut()) {
                $operations[] = $operation;
            }
            if ($operation = $pathItem->getDelete()) {
                $operations[] = $operation;
            }

            foreach ($operations as $operation) {
                foreach ($operation->getTags() as $tagName) {
                    if (! isset($paths[$tagName])) {
                        $paths[$tagName] = [];
                    }
                    $paths[$tagName][$path] = $pathItem;
                }
            }
        }

        $paths = array_filter($paths, static fn ($key) => strtolower((string) ((new AsciiSlugger())->slug($key))) === $tag, ARRAY_FILTER_USE_KEY);

        return $this->render('http_documentation/tag.html.twig', [
            'paths' => current(array_values($paths)),
            'tag' => current(array_keys($paths)),
        ]);
    }
}
