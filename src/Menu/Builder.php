<?php

declare(strict_types=1);

namespace App\Menu;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class Builder
{
    private OpenApi $openApi;

    public function __construct(private FactoryInterface $factory, private OpenApiFactoryInterface $openApiFactory)
    {
        $this->openApi = ($this->openApiFactory)([]);
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('APIs')
            ->setLabelAttribute('class', 'item__label')
        ;

        $paths = [];
        /**
         * @var string $path
         * @var PathItem $pathItem
         */
        foreach ($this->openApi->getPaths()->getPaths() as $path => $pathItem) {
            $operations = [];

            if (($operation = $pathItem->getGet()) !== null) {
                $operations[] = $operation;
            }
            if (($operation = $pathItem->getPost()) !== null) {
                $operations[] = $operation;
            }
            if (($operation = $pathItem->getPatch()) !== null) {
                $operations[] = $operation;
            }
            if (($operation = $pathItem->getPut()) !== null) {
                $operations[] = $operation;
            }
            if (($operation = $pathItem->getDelete()) !== null) {
                $operations[] = $operation;
            }

            foreach ($operations as $operation) {
                foreach ($operation->getTags() as $tag) {
                    if (! isset($paths[$tag])) {
                        $paths[$tag] = [];
                    }
                    $paths[$tag][$path] = $pathItem;
                }
            }
        }

        foreach (array_keys($paths) as $tag) {
            $menu->addChild($tag, [
                'route' => 'app.tag',
                'routeParameters' => ['tag' => strtolower((string) ((new AsciiSlugger())->slug($tag)))],
            ])
                ->setLabelAttribute('class', 'item__label')
                ->setLinkAttribute('class', 'item__link')
            ;
        }

        $menu->addChild('Manager')
            ->setLabelAttribute('class', 'item__label')
        ;

        $menu->addChild('EasyAdmin', [
            'route' => 'admin',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $menu->addChild('Documentations')
            ->setLabelAttribute('class', 'item__label')
        ;

        $menu->addChild('OpenAPI', [
            'route' => 'api_doc',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $menu->addChild('ReDoc', [
            'route' => 'api_doc',
            'routeParameters' => ['ui' => 're_doc'],
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $menu->addChild('GraphiQL', [
            'route' => 'api_graphql_graphiql',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $menu->addChild('GraphQL Playground', [
            'route' => 'api_graphql_graphql_playground',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        return $menu;
    }
}
