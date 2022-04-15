<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

final class Builder
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Mock')
            ->setLabelAttribute('class', 'item__label')
        ;

        foreach (['User', 'Album', 'Photo', 'Post', 'Comment', 'Todo'] as $tag) {
            $menu->addChild($tag, [
                'route' => 'app.tag',
                'routeParameters' => ['tag' => strtolower((string) ((new AsciiSlugger())->slug($tag)))],
            ])
                ->setLabelAttribute('class', 'item__label')
                ->setLinkAttribute('class', 'item__link')
            ;
        }

        $manager = $menu->addChild('Manager')
            ->setLabelAttribute('class', 'item__label')
        ;

        $manager->addChild('EasyAdmin', [
            'route' => 'admin',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $documentation = $menu->addChild('Documentations')
            ->setLabelAttribute('class', 'item__label')
        ;

        $documentation->addChild('OpenAPI', [
            'route' => 'api_doc',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $documentation->addChild('ReDoc', [
            'route' => 'api_doc',
            'routeParameters' => ['ui' => 're_doc'],
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $documentation->addChild('GraphiQL', [
            'route' => 'api_graphql_graphiql',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $documentation->addChild('GraphQL Playground', [
            'route' => 'api_graphql_graphql_playground',
        ])->setLinkAttribute('target', '_blank')->setLinkAttribute('class', 'item__link');

        $menu->addChild('Bin')
            ->setLabelAttribute('class', 'item__label')
        ;

        $menu->addChild('Buckets', [
            'route' => 'inspect.usage',
            'routeParameters' => ['bin' => Uuid::v4()->toRfc4122()],
        ])->setLinkAttribute('class', 'item__link');

        foreach (['Anything', 'Cookies', 'Dynamic data', 'Requests', 'Status codes', 'Images'] as $tag) {
            $menu->addChild($tag, [
                'route' => 'app.tag',
                'routeParameters' => ['tag' => strtolower((string) ((new AsciiSlugger())->slug($tag)))],
            ])
                ->setLabelAttribute('class', 'item__label')
                ->setLinkAttribute('class', 'item__link')
            ;
        }

        return $menu;
    }
}
