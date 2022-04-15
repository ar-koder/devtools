<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\Controller\API\CookieController;
use App\Controller\API\GeneratorController;
use App\Controller\API\ImageController;
use App\Controller\API\RequestController;
use App\Controller\API\StatusController;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        CookieController::setupOpenApiDocumentation($openApi);
        GeneratorController::setupOpenApiDocumentation($openApi);
        ImageController::setupOpenApiDocumentation($openApi);
        RequestController::setupOpenApiDocumentation($openApi);
        StatusController::setupOpenApiDocumentation($openApi);

        return $openApi;
    }
}
